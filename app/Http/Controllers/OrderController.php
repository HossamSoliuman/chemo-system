<?php

namespace App\Http\Controllers;

use App\Models\Diagnosis;
use App\Models\Order;
use App\Models\OrderDrug;
use App\Models\Patient;
use App\Models\PatientCumulativeDose;
use App\Models\Protocol;
use App\Services\ClinicalCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(private ClinicalCalculationService $calc) {}

    public function index(Request $request)
    {
        $query = Order::with(['patient', 'protocol.diagnosis']);

        if ($request->filled('mrn')) {
            $query->whereHas('patient', fn($q) => $q->where('mrn', 'like', '%' . $request->mrn . '%'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('ordered_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('ordered_at', '<=', $request->date_to);
        }
        if ($request->filled('protocol_id')) {
            $query->where('protocol_id', $request->protocol_id);
        }

        $orders = $query->orderByDesc('ordered_at')->paginate(20)->withQueryString();
        $protocols = Protocol::orderBy('name')->get();

        return view('orders.index', compact('orders', 'protocols'));
    }

    public function create()
    {
        $diagnoses = Diagnosis::orderBy('name')->get();
        return view('orders.create', compact('diagnoses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'protocol_id' => 'required|exists:protocols,id',
            'dose_modification_percent' => 'required|numeric|min:1|max:200',
            'consultant_name' => 'nullable|string|max:255',
            'pharmacist_name' => 'nullable|string|max:255',
            'nurse_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'drugs' => 'required|array',
        ]);

        $patient = Patient::findOrFail($request->patient_id);
        $protocol = Protocol::with('protocolDrugs.drug')->findOrFail($request->protocol_id);

        $age = $this->calc->calculateAge($patient->date_of_birth->toDateTime());
        $bsa = $this->calc->calculateBSA($patient->height_cm, $patient->weight_kg);
        $crcl = $this->calc->calculateCrCl($age, $patient->weight_kg, $patient->serum_creatinine, $patient->gender);
        $cycleInfo = $this->calc->determineCycleNumber($patient, $protocol);
        $modPct = (float) $request->dose_modification_percent;

        $orderDrugsData = [];
        foreach ($protocol->protocolDrugs as $pd) {
            $doseResult = $this->calc->calculateDrugDose($pd, $bsa, $crcl, $modPct);
            $submittedDrug = collect($request->drugs)->firstWhere('protocol_drug_id', $pd->id);

            $finalDose = $doseResult['final'];
            $isOverridden = false;
            $overrideReason = null;

            if ($submittedDrug && isset($submittedDrug['final_dose']) && (float)$submittedDrug['final_dose'] != $doseResult['final']) {
                $finalDose = (float) $submittedDrug['final_dose'];
                $isOverridden = true;
                $overrideReason = $submittedDrug['override_reason'] ?? null;
            }

            $orderDrugsData[] = [
                'protocol_drug' => $pd,
                'calculated' => $doseResult['calculated'],
                'final' => $finalDose,
                'cap_applied' => $doseResult['cap_applied'],
                'is_included' => isset($submittedDrug['is_included']) ? (bool)$submittedDrug['is_included'] : true,
                'is_manually_overridden' => $isOverridden,
                'override_reason' => $overrideReason,
            ];
        }

        $lifetimeWarnings = $this->calc->checkLifetimeCaps($patient, collect($orderDrugsData));

        if (!empty($lifetimeWarnings) && !$request->boolean('lifetime_cap_acknowledged')) {
            return response()->json([
                'lifetime_warnings' => $lifetimeWarnings,
                'requires_acknowledgment' => true,
            ]);
        }

        $isModified = $modPct != 100 || collect($orderDrugsData)->contains('is_manually_overridden', true);

        $order = DB::transaction(function () use ($request, $patient, $protocol, $bsa, $crcl, $cycleInfo, $modPct, $isModified, $orderDrugsData) {
            $order = Order::create([
                'patient_id' => $patient->id,
                'protocol_id' => $protocol->id,
                'cycle_number' => $cycleInfo['cycle_number'],
                'is_same_cycle' => $cycleInfo['is_same_cycle'],
                'parent_order_id' => $cycleInfo['parent_order_id'],
                'bsa' => $bsa,
                'crcl' => $crcl,
                'dose_modification_percent' => $modPct,
                'dose_modification_reason' => $request->dose_modification_reason,
                'is_modified_protocol' => $isModified,
                'consultant_name' => $request->consultant_name,
                'pharmacist_name' => $request->pharmacist_name,
                'nurse_name' => $request->nurse_name,
                'ordered_at' => now(),
                'notes' => $request->notes,
                'status' => 'draft',
            ]);

            foreach ($orderDrugsData as $item) {
                $pd = $item['protocol_drug'];
                OrderDrug::create([
                    'order_id' => $order->id,
                    'protocol_drug_id' => $pd->id,
                    'drug_id' => $pd->drug_id,
                    'category' => $pd->category,
                    'calculated_dose' => $item['calculated'],
                    'final_dose' => $item['final'],
                    'is_included' => $item['is_included'],
                    'is_manually_overridden' => $item['is_manually_overridden'],
                    'override_reason' => $item['override_reason'],
                    'cap_applied' => $item['cap_applied'],
                ]);
            }

            return $order;
        });

        if ($request->expectsJson()) {
            return response()->json(['redirect' => route('orders.show', $order)]);
        }

        return redirect()->route('orders.show', $order)->with('success', 'Order created as draft.');
    }

    public function show(Order $order)
    {
        $order->load(['patient', 'protocol.diagnosis', 'orderDrugs.drug', 'orderDrugs.protocolDrug']);
        return view('orders.show', compact('order'));
    }

    public function confirm(Request $request, Order $order)
    {
        if ($order->status !== 'draft') {
            return back()->with('error', 'Only draft orders can be confirmed.');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'confirmed']);

            foreach ($order->orderDrugs()->where('is_included', true)->get() as $od) {
                PatientCumulativeDose::updateOrCreate(
                    ['patient_id' => $order->patient_id, 'drug_id' => $od->drug_id],
                    ['total_dose' => DB::raw('total_dose + ' . $od->final_dose), 'updated_at' => now()]
                );
            }
        });

        return redirect()->route('orders.show', $order)->with('success', 'Order confirmed successfully.');
    }

    public function print(Order $order)
    {
        $order->load(['patient', 'protocol.diagnosis', 'orderDrugs.drug', 'orderDrugs.protocolDrug']);
        if ($order->status === 'confirmed') {
            $order->update(['status' => 'printed']);
        }
        return view('orders.print', compact('order'));
    }

    public function destroy(Order $order)
    {
        if ($order->status === 'confirmed') {
            return back()->with('error', 'Confirmed orders cannot be deleted without admin action.');
        }
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }
}
