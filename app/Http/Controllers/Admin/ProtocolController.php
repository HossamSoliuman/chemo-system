<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Drug;
use App\Models\Protocol;
use App\Models\ProtocolDrug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProtocolController extends Controller
{
    public function index()
    {
        $protocols = Protocol::with('diagnosis')->orderBy('name')->paginate(20);
        return view('admin.protocols.index', compact('protocols'));
    }

    public function create()
    {
        $diagnoses = Diagnosis::orderBy('name')->get();
        $drugs = Drug::orderBy('name')->get();
        return view('admin.protocols.create', compact('diagnoses', 'drugs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'cycle_duration_days' => 'required|integer|min:1',
            'drugs' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $protocol = Protocol::create([
                'name' => $request->name,
                'diagnosis_id' => $request->diagnosis_id,
                'description' => $request->description,
                'tests_reminder' => $request->tests_reminder,
                'cycle_duration_days' => $request->cycle_duration_days,
            ]);

            $this->syncProtocolDrugs($protocol, $request->drugs ?? []);
        });

        return redirect()->route('admin.protocols.index')->with('success', 'Protocol created successfully.');
    }

    public function edit(Protocol $protocol)
    {
        $diagnoses = Diagnosis::orderBy('name')->get();
        $drugs = Drug::orderBy('name')->get();
        $protocol->load(['protocolDrugs.drug']);
        return view('admin.protocols.edit', compact('protocol', 'diagnoses', 'drugs'));
    }

    public function update(Request $request, Protocol $protocol)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'diagnosis_id' => 'required|exists:diagnoses,id',
            'cycle_duration_days' => 'required|integer|min:1',
            'drugs' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $protocol) {
            $protocol->update([
                'name' => $request->name,
                'diagnosis_id' => $request->diagnosis_id,
                'description' => $request->description,
                'tests_reminder' => $request->tests_reminder,
                'cycle_duration_days' => $request->cycle_duration_days,
            ]);

            $protocol->protocolDrugs()->delete();
            $this->syncProtocolDrugs($protocol, $request->drugs ?? []);
        });

        return redirect()->route('admin.protocols.index')->with('success', 'Protocol updated successfully.');
    }

    public function destroy(Protocol $protocol)
    {
        $protocol->delete();
        return redirect()->route('admin.protocols.index')->with('success', 'Protocol deleted.');
    }

    private function syncProtocolDrugs(Protocol $protocol, array $drugsData): void
    {
        foreach ($drugsData as $index => $drugData) {
            if (empty($drugData['drug_id'])) {
                continue;
            }
            ProtocolDrug::create([
                'protocol_id'       => $protocol->id,
                'drug_id'           => $drugData['drug_id'],
                'category'          => $drugData['category'] ?? 'chemotherapy',
                'dose_type'         => $drugData['dose_type'] ?? 'fixed',
                'dose_per_unit'     => $drugData['dose_per_unit'] ?? null,
                'dose_label'        => $drugData['dose_label'] ?? null,
                'fixed_dose'        => $drugData['fixed_dose'] ?? null,
                'target_auc'        => $drugData['target_auc'] ?? null,
                'per_cycle_cap'     => $drugData['per_cycle_cap'] ?? null,
                'per_cycle_cap_unit' => $drugData['per_cycle_cap_unit'] ?? null,
                'lifetime_cap'      => $drugData['lifetime_cap'] ?? null,
                'lifetime_cap_unit' => $drugData['lifetime_cap_unit'] ?? null,
                'route'             => $drugData['route'] ?? null,
                'frequency'         => $drugData['frequency'] ?? null,
                'duration_days'     => $drugData['duration_days'] ?? null,
                'notes'             => $drugData['notes'] ?? null,
                'sort_order'        => $index,
            ]);
        }
    }
}
