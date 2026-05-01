<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemotherapy Order — {{ $order->order_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; background: #fff; padding: 1cm; }
        .no-print { display: block; }
        @media print {
            @page { margin: 1cm; size: A4; }
            .no-print { display: none !important; }
            body { padding: 0; }
        }
        table { border-collapse: collapse; width: 100%; }
        td, th { padding: 3px 5px; vertical-align: top; }
        .border-all td, .border-all th { border: 1px solid #000; }
        .outer-box { border: 2px solid #000; margin-bottom: 6px; }
        .section-header { background: #000; color: #fff; font-weight: bold; font-size: 10px; padding: 3px 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .field-label { font-weight: bold; white-space: nowrap; }
        .field-line { border-bottom: 1px solid #000; min-width: 80px; display: inline-block; }
        .header-left { border-right: 2px solid #000; padding: 8px; }
        .header-right { padding: 8px; }
        .protocol-title { font-size: 16px; font-weight: bold; text-align: center; margin-bottom: 2px; }
        .protocol-arabic { font-size: 13px; text-align: center; margin-bottom: 6px; }
        .modified-banner { border: 2px solid #cc0000; background: #fff0f0; padding: 4px 8px; margin-bottom: 6px; display: flex; align-items: center; gap: 8px; }
        .drug-table th { background: #f0f0f0; font-weight: bold; font-size: 10px; border: 1px solid #000; padding: 3px 4px; text-align: left; }
        .drug-table td { border: 1px solid #000; padding: 3px 4px; font-size: 11px; }
        .drug-table tr.excluded td { color: #999; text-decoration: line-through; }
        .chemo-section { border: 2px solid #000; margin-bottom: 6px; }
        .chemo-header { background: #000; color: #fff; font-weight: bold; padding: 3px 6px; font-size: 11px; }
        .sig-table td { border: 1px solid #000; padding: 8px 6px; }
        .dose-mod-flag { background: #fff3cd; border: 1px solid #ffc107; padding: 2px 6px; font-size: 10px; font-weight: bold; display: inline-block; }
        .split-badge { background: #e3f2fd; border: 1px solid #1976d2; color: #1976d2; padding: 2px 6px; font-size: 10px; font-weight: bold; display: inline-block; }
    </style>
</head>
<body>

<div class="no-print" style="margin-bottom:12px; display:flex; gap:8px;">
    <button onclick="window.print()" style="background:#2563eb;color:#fff;border:none;padding:7px 18px;border-radius:6px;cursor:pointer;font-size:13px;">
        &#128438; Print
    </button>
    <a href="{{ route('orders.show', $order) }}" style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:7px 18px;border-radius:6px;text-decoration:none;font-size:13px;">
        &#8592; Back
    </a>
</div>

{{-- Modified Protocol Banner --}}
@if($order->is_modified_protocol)
<div class="modified-banner">
    <span style="font-size:18px;">&#9888;</span>
    <div>
        <strong style="color:#cc0000;font-size:12px;text-transform:uppercase;">Modified Protocol Order</strong>
        @if($order->dose_modification_reason)
        <div style="font-size:11px;color:#cc0000;">Reason: {{ $order->dose_modification_reason }}</div>
        @endif
    </div>
    <div style="margin-left:auto;font-size:13px;font-weight:bold;color:#cc0000;">
        Dose Modified Order
    </div>
</div>
@endif

{{-- HEADER --}}
<div class="outer-box">
    <table style="width:100%;">
        <tr>
            <td class="header-left" style="width:38%; vertical-align:middle;">
                <div style="font-size:9px; font-weight:bold; color:#555;">{{ env('HOSPITAL_NAME', 'General Oncology Center') }}</div>
                <div class="protocol-title">CHEMOTHERAPY PROTOCOL</div>
                <div class="protocol-arabic">بروتوكول العلاج الكيميائي</div>
                <div style="font-size:10px; color:#555;">{{ env('HOSPITAL_ADDRESS', '') }}</div>
            </td>
            <td class="header-right" style="vertical-align:top;">
                <table style="width:100%; border-collapse:collapse;">
                    <tr>
                        <td style="width:160px;"><span class="field-label">Medical Record Number:</span></td>
                        <td><span class="field-line" style="min-width:120px;">{{ $order->patient->mrn }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="field-label">Name:</span></td>
                        <td><span class="field-line" style="min-width:200px;">{{ $order->patient->name }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="field-label">Age:</span></td>
                        <td>
                            {{ $order->patient->age }} yrs &nbsp;&nbsp;&nbsp;
                            <span class="field-label">Sex:</span>
                            {{ $order->patient->gender === 'male' ? '&#9746; M &nbsp; &#9744; F' : '&#9744; M &nbsp; &#9746; F' }}
                        </td>
                    </tr>
                    <tr>
                        <td><span class="field-label">Consultant In-charge:</span></td>
                        <td><span class="field-line" style="min-width:160px;">{{ $order->consultant_name ?? '' }}</span></td>
                    </tr>
                    <tr>
                        <td><span class="field-label">Dept / Unit:</span></td>
                        <td>
                            <span class="field-line" style="min-width:90px;">&nbsp;</span>
                            &nbsp; <span class="field-label">Room / Bed:</span>
                            <span class="field-line" style="min-width:60px;">&nbsp;</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div>

{{-- PATIENT CLINICAL INFO --}}
<div class="outer-box">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="padding:4px 6px; width:33%; border-right:1px solid #ccc;">
                <span class="field-label">Pregnant:</span> &#9744; Yes &nbsp; &#9744; No &nbsp;&nbsp;
                <span class="field-label">Lactating:</span> &#9744; Yes &nbsp; &#9744; No
            </td>
            <td style="padding:4px 6px; width:34%; border-right:1px solid #ccc;">
                <span class="field-label">Allergy:</span> &#9744; Yes &nbsp; &#9744; No &nbsp;
                <span class="field-label">If yes:</span> <span class="field-line" style="min-width:80px;">{{ $order->notes ?? '' }}</span>
            </td>
            <td style="padding:4px 6px; width:33%;">
                <span class="field-label">ECOG Status:</span> <span class="field-line" style="min-width:60px;"></span>
            </td>
        </tr>
        <tr style="border-top:1px solid #ccc;">
            <td colspan="2" style="padding:4px 6px; border-right:1px solid #ccc;">
                <span class="field-label">Diagnosis:</span> {{ $order->protocol->diagnosis->name }}
                &nbsp;&nbsp; <span class="field-label">Stage:</span> <span class="field-line" style="min-width:50px;"></span>
            </td>
            <td style="padding:4px 6px;">
                <span class="field-label">ECOG=</span><span class="field-line" style="min-width:40px;"></span>
            </td>
        </tr>
        <tr style="border-top:1px solid #ccc;">
            <td colspan="2" style="padding:4px 6px; border-right:1px solid #ccc;">
                <span class="field-label">Protocol:</span> {{ $order->protocol->name }}
                @if($order->is_split_cycle && $order->cycle_day_week)
                    &nbsp; <span class="split-badge">{{ $order->cycle_day_week }}</span>
                @endif
            </td>
            <td style="padding:4px 6px;"><span class="field-label">SETTING OF CHEMOTHERAPY:</span></td>
        </tr>
        <tr style="border-top:1px solid #ccc;">
            <td colspan="3" style="padding:4px 6px;">
                <span class="field-label">Ht:</span> {{ $order->patient->height_cm }} cm &nbsp;&nbsp;&nbsp;
                <span class="field-label">Wt:</span> {{ $order->patient->weight_kg }} kg &nbsp;&nbsp;&nbsp;
                <span class="field-label">BSA:</span> {{ $order->bsa }} m² &nbsp;&nbsp;&nbsp;
                <span class="field-label">CrCl:</span> {{ $order->crcl }} mL/min &nbsp;&nbsp;&nbsp;
                <span class="field-label">Cycle:</span> {{ $order->cycle_number }}
                @if($order->is_same_cycle) <small>(same cycle)</small> @endif
                &nbsp;&nbsp;&nbsp;
                <span class="field-label">Date:</span> {{ $order->ordered_at->format('d/m/Y') }}
            </td>
        </tr>
        @if($order->is_modified_protocol)
        <tr style="border-top:1px solid #ccc; background:#fff8e1;">
            <td colspan="3" style="padding:4px 6px;">
                <span class="field-label">Dose modification for:</span>
                &#9746; Other Toxicity: {{ $order->dose_modification_reason ?? '—' }}
            </td>
        </tr>
        @else
        <tr style="border-top:1px solid #ccc;">
            <td colspan="3" style="padding:4px 6px;">
                <span class="field-label">Dose modification for:</span>
                &#9744; Cardiology &nbsp;&nbsp; &#9744; Other Toxicity <span class="field-line" style="min-width:120px;"></span>
            </td>
        </tr>
        @endif
    </table>
</div>

{{-- PRE-MEDICATIONS --}}
@php $preDrugs = $order->orderDrugs->where('category', 'pre_medication')->where('is_included', true)->values(); @endphp
@if($preDrugs->count() > 0)
<div class="outer-box">
    <div class="section-header">Premedication (given 30 min before chemotherapy)</div>
    <div style="padding:6px;">
        <table class="drug-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th style="width:30%;">Drug</th>
                    <th style="width:14%;">Dose</th>
                    <th style="width:14%;">Frequency</th>
                    <th style="width:20%;">Route</th>
                    <th style="width:18%;">Duration / Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($preDrugs as $i => $od)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td><strong>{{ $od->drug->name }}</strong></td>
                    <td><strong>{{ number_format($od->final_dose, 2) }}</strong> {{ $od->drug->unit }}</td>
                    <td>{{ $od->protocolDrug->frequency ?? '—' }}</td>
                    <td>{{ $od->protocolDrug->route ?? '—' }}</td>
                    <td>{{ $od->protocolDrug->duration_days ? $od->protocolDrug->duration_days . ' day(s)' : ($od->protocolDrug->notes ?? '—') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- CHEMOTHERAPY --}}
@php $chemoDrugs = $order->orderDrugs->where('category', 'chemotherapy')->where('is_included', true)->values(); @endphp
@if($chemoDrugs->count() > 0)
<div class="chemo-section">
    <div class="chemo-header">Chemotherapy</div>
    <div style="padding:6px;">
        <table class="drug-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:22%;">Drug</th>
                    <th style="width:12%;">mg/m² or Basis</th>
                    <th style="width:8%;">Mod %</th>
                    <th style="width:14%;">Final Dose</th>
                    <th style="width:10%;">Route</th>
                    <th style="width:16%;">Frequency / Day</th>
                    <th style="width:18%;">Notes / Flags</th>
                </tr>
            </thead>
            <tbody>
                @foreach($chemoDrugs as $od)
                @php
                    $pd = $od->protocolDrug;
                    $doseUnitLabel = match($pd->dose_type) {
                        'bsa_based' => number_format($pd->dose_per_unit, 2) . ' mg/m²',
                        'weight_based' => number_format($pd->dose_per_unit, 2) . ' mg/kg',
                        'carboplatin_calvert' => 'AUC ' . $pd->target_auc,
                        'fixed' => 'Fixed',
                        default => '—',
                    };
                    $modDisplay = null;
                    if ($od->calculated_dose > 0 && $od->final_dose != $od->calculated_dose) {
                        $modDisplay = round(($od->final_dose / $od->calculated_dose) * 100) . '%';
                    }
                @endphp
                <tr>
                    <td>
                        <strong>{{ $od->drug->name }}</strong>
                        @if($od->cap_applied) <br><small style="color:#b45309;">(cap applied)</small> @endif
                        @if($od->is_manually_overridden) <br><small style="color:#c2410c;">&#9998; Override{{ $od->override_reason ? ': '.$od->override_reason : '' }}</small> @endif
                    </td>
                    <td style="color:#1d4ed8; font-weight:bold;">{{ $doseUnitLabel }}</td>
                    <td style="{{ $modDisplay && $modDisplay !== '100%' ? 'color:#c2410c; font-weight:bold;' : '' }}">
                        {{ $modDisplay ?? '100%' }}
                    </td>
                    <td style="font-weight:bold; font-size:12px;">
                        {{ number_format($od->final_dose, 2) }} {{ $od->drug->unit }}
                        @if($od->protocolDrug->per_cycle_cap)
                            <br><small style="color:#666;">Cap: {{ number_format($od->protocolDrug->per_cycle_cap,2) }} {{ $od->protocolDrug->per_cycle_cap_unit }}</small>
                        @endif
                        @if($od->protocolDrug->lifetime_cap)
                            <br><small style="color:#dc2626;">Life cap: {{ number_format($od->protocolDrug->lifetime_cap,2) }} {{ $od->protocolDrug->lifetime_cap_unit }}</small>
                        @endif
                    </td>
                    <td>{{ $pd->route ?? '—' }}</td>
                    <td>{{ $pd->frequency ?? '—' }}
                        @if($pd->duration_days) <br><small>{{ $pd->duration_days }} day(s)</small> @endif
                    </td>
                    <td style="font-size:10px; color:#555;">{{ $pd->notes ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- POST-MEDICATIONS --}}
@php $postDrugs = $order->orderDrugs->where('category', 'post_medication')->where('is_included', true)->values(); @endphp
@if($postDrugs->count() > 0)
<div class="outer-box">
    <div class="section-header">Post-Chemotherapy Medications</div>
    <div style="padding:6px;">
        <table class="drug-table" style="width:100%;">
            <thead>
                <tr>
                    <th style="width:4%;">#</th>
                    <th style="width:30%;">Drug</th>
                    <th style="width:14%;">Dose</th>
                    <th style="width:14%;">Frequency</th>
                    <th style="width:20%;">Route</th>
                    <th style="width:18%;">Duration / Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($postDrugs as $i => $od)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td><strong>{{ $od->drug->name }}</strong></td>
                    <td><strong>{{ number_format($od->final_dose, 2) }}</strong> {{ $od->drug->unit }}</td>
                    <td>{{ $od->protocolDrug->frequency ?? '—' }}</td>
                    <td>{{ $od->protocolDrug->route ?? '—' }}</td>
                    <td>{{ $od->protocolDrug->duration_days ? $od->protocolDrug->duration_days . ' day(s)' : ($od->protocolDrug->notes ?? '—') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- EXCLUDED DRUGS NOTE --}}
@php $excluded = $order->orderDrugs->where('is_included', false); @endphp
@if($excluded->count() > 0)
<div style="border:1px solid #ccc; padding:4px 8px; margin-bottom:6px; font-size:10px; color:#666;">
    <strong>Excluded from this order:</strong> {{ $excluded->pluck('drug.name')->implode(', ') }}
</div>
@endif

{{-- SIGNATURE BLOCK --}}
<div class="outer-box">
    <table style="width:100%; border-collapse:collapse;">
        <tr>
            <td style="border:1px solid #000; padding:10px 8px; width:33%; vertical-align:bottom;">
                <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:28px;">Consultant Stamp / Signature</div>
                <div style="border-top:1px solid #000; padding-top:2px; display:flex; gap:20px; font-size:10px;">
                    <span><strong>Date:</strong> {{ $order->ordered_at->format('d/m/Y') }}</span>
                    <span><strong>Time:</strong> {{ $order->ordered_at->format('H:i') }}</span>
                </div>
                @if($order->consultant_name)
                <div style="font-size:10px; margin-top:2px;">{{ $order->consultant_name }}</div>
                @endif
            </td>
            <td style="border:1px solid #000; padding:10px 8px; width:33%; vertical-align:bottom; border-left:none;">
                <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:28px;">Pharmacist Stamp / Signature</div>
                <div style="border-top:1px solid #000; padding-top:2px; display:flex; gap:20px; font-size:10px;">
                    <span><strong>Date:</strong> ___________</span>
                    <span><strong>Time:</strong> _______</span>
                </div>
                @if($order->pharmacist_name)
                <div style="font-size:10px; margin-top:2px;">{{ $order->pharmacist_name }}</div>
                @endif
            </td>
            <td style="border:1px solid #000; padding:10px 8px; width:34%; vertical-align:bottom; border-left:none;">
                <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:28px;">Noted By (Nurse) Stamp / Signature</div>
                <div style="border-top:1px solid #000; padding-top:2px; display:flex; gap:20px; font-size:10px;">
                    <span><strong>Date:</strong> ___________</span>
                    <span><strong>Time:</strong> _______</span>
                </div>
                @if($order->nurse_name)
                <div style="font-size:10px; margin-top:2px;">{{ $order->nurse_name }}</div>
                @endif
            </td>
        </tr>
    </table>
</div>

<div style="text-align:center; font-size:9px; color:#666; margin-top:4px;">
    Chemotherapy Protocol — Page 1 of 1 &nbsp;|&nbsp; {{ $order->order_number }} &nbsp;|&nbsp;
    Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
    {{ env('HOSPITAL_NAME') }} &nbsp;|&nbsp; CONFIDENTIAL MEDICAL DOCUMENT
</div>

</body>
</html>
