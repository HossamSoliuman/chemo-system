<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chemotherapy Order — {{ $order->order_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
            padding: 1cm;
        }

        .no-print {
            display: block;
        }

        @media print {
            @page {
                margin: 1cm;
                size: A4;
            }

            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .outer-box {
            border: 1.5px solid #000;
            margin-bottom: 5px;
        }

        .section-header {
            font-weight: bold;
            font-size: 11px;
            padding: 3px 6px;
            text-decoration: underline;
        }

        .chemo-header {
            font-weight: bold;
            font-size: 11px;
            padding: 3px 6px;
        }

        .field-label {
            font-weight: bold;
        }

        .field-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 80px;
        }

        .cb {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            vertical-align: middle;
            text-align: center;
            font-size: 9px;
            line-height: 10px;
        }

        .cb-checked::after {
            content: '✓';
        }

        .sig-cell {
            border: 1px solid #000;
            padding: 8px 8px 6px 8px;
            width: 33.33%;
            vertical-align: bottom;
        }

        .sig-cell+.sig-cell {
            border-left: none;
        }

        /* Chemo narrative line */
        .chemo-line {
            padding: 3px 6px;
            line-height: 1.6;
            font-size: 11px;
        }

        .chemo-line+.chemo-line {
            border-top: none;
        }

        .chemo-notes-line {
            padding: 2px 6px 3px 6px;
            font-size: 10px;
            color: #333;
            font-style: italic;
        }

        /* Pre/post med numbered list */
        .med-list {
            padding: 4px 8px 6px 24px;
        }

        .med-list li {
            margin-bottom: 3px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    {{-- Print / Back Buttons --}}
    <div class="no-print" style="margin-bottom:12px; display:flex; gap:8px;">
        <button onclick="window.print()"
            style="background:#2563eb;color:#fff;border:none;padding:7px 18px;border-radius:6px;cursor:pointer;font-size:13px;">
            &#128438; Print
        </button>
        <a href="{{ route('orders.show', $order) }}"
            style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:7px 18px;border-radius:6px;text-decoration:none;font-size:13px;">
            &#8592; Back
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════
     HEADER
═══════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        <table style="width:100%;">
            <tr>
                {{-- Left: Hospital identity --}}
                <td style="width:36%; border-right:1.5px solid #000; padding:8px; vertical-align:middle;">
                    {{-- Logo placeholder — swap the src for the real logo asset --}}
                    @if (env('HOSPITAL_LOGO'))
                        <img src="{{ env('HOSPITAL_LOGO') }}" style="height:38px; margin-bottom:4px; display:block;">
                    @else
                        <div style="font-size:9px; color:#555; margin-bottom:2px;">
                            {{ env('HOSPITAL_NAME', 'General Oncology Center') }}</div>
                        <div style="font-size:9px; color:#555;">{{ env('HOSPITAL_SUBTITLE', '') }}</div>
                    @endif
                    <div style="font-size:15px; font-weight:bold; margin-top:4px;">CHEMOTHERAPY PROTOCOL</div>
                    <div style="font-size:12px; margin-top:2px;">بروتوكول العلاج الكيميائي</div>
                </td>

                {{-- Right: Patient identifiers --}}
                <td style="padding:6px 10px; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="width:175px;" class="field-label">Medical Record Number</td>
                            <td><span class="field-line" style="min-width:150px;">{{ $order->patient->mrn }}</span></td>
                        </tr>
                        <tr>
                            <td class="field-label">Name:</td>
                            <td><span class="field-line" style="min-width:200px;">{{ $order->patient->name }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label">Age:</td>
                            <td>
                                <span class="field-line" style="min-width:40px;">{{ $order->patient->age }}</span>
                                yrs/mos/day(s) &nbsp;&nbsp;
                                <span class="field-label">Sex</span> &nbsp;
                                <span class="cb {{ $order->patient->gender === 'male' ? 'cb-checked' : '' }}"></span> M
                                &nbsp;
                                <span class="cb {{ $order->patient->gender === 'female' ? 'cb-checked' : '' }}"></span>
                                F
                            </td>
                        </tr>
                        <tr>
                            <td class="field-label">Nationality:</td>
                            <td><span class="field-line"
                                    style="min-width:160px;">{{ $order->patient->nationality ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="field-label">Consultant In-charge:</td>
                            <td><span class="field-line"
                                    style="min-width:160px;">{{ $order->consultant_name ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td class="field-label">Dept/Unit:</td>
                            <td>
                                <span class="field-line" style="min-width:90px;">{{ $order->dept_unit ?? '' }}</span>
                                &nbsp;&nbsp; <span class="field-label">Room/Bed No.</span>
                                <span class="field-line" style="min-width:60px;">{{ $order->room_bed ?? '' }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════
     PATIENT CLINICAL INFO BLOCK
═══════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        <table style="width:100%; border-collapse:collapse;">

            {{-- Row 1: Pregnant / Lactating / Allergy --}}
            <tr>
                <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #ccc;">
                    <span class="field-label">Pregnant:</span>
                    <span class="cb"></span> Yes &nbsp; <span class="cb"></span> No &nbsp;&nbsp;&nbsp;
                    <span class="field-label">Lactating:</span>
                    <span class="cb"></span> Yes &nbsp; <span class="cb"></span> No &nbsp;&nbsp;&nbsp;
                    <span class="field-label">Allergy:</span>
                    <span class="cb"></span> Yes &nbsp; <span class="cb"></span> No &nbsp;&nbsp;
                    <span class="field-label">If yes Specify</span>
                    <span class="field-line" style="min-width:120px;">{{ $order->notes ?? '' }}</span>
                </td>
            </tr>

            {{-- Row 2: Diagnosis / Stage / ECOG --}}
            <tr>
                <td colspan="2"
                    style="padding:4px 6px; border-right:1px solid #ccc; border-bottom:1px solid #ccc; width:70%;">
                    <span class="field-label">Diagnosis:</span>
                    {{ $order->protocol->diagnosis->name }}
                    &nbsp;&nbsp;&nbsp;
                    <span class="field-label">Stage:</span>
                    <span class="field-line" style="min-width:60px;"></span>
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #ccc;">
                    <span class="field-label">ECOG=</span><span class="field-line" style="min-width:50px;"></span>
                </td>
            </tr>

            {{-- Row 3: Protocol / Setting --}}
            <tr>
                <td colspan="2" style="padding:4px 6px; border-right:1px solid #ccc; border-bottom:1px solid #ccc;">
                    <span class="field-label">Protocol:</span>
                    {{ $order->protocol->name }}
                    @if ($order->is_split_cycle && $order->cycle_day_week)
                        &nbsp;
                        <span
                            style="background:#e3f2fd;border:1px solid #1976d2;color:#1976d2;padding:1px 5px;font-size:10px;font-weight:bold;">
                            {{ $order->cycle_day_week }}
                        </span>
                    @endif
                </td>
                <td style="padding:4px 6px; border-bottom:1px solid #ccc;">
                    <span class="field-label">SETTING OF CHEMOTHERAPY:</span>
                </td>
            </tr>

            {{-- Row 4: Ht / Wt / BSA / CrCl / Cycle / Date --}}
            <tr>
                <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #ccc;">
                    <span class="field-label">Ht:</span>
                    <span class="field-line" style="min-width:40px;">{{ $order->patient->height_cm }}</span> cm
                    &nbsp;&nbsp;&nbsp;
                    <span class="field-label">Wt:</span>
                    <span class="field-line" style="min-width:40px;">{{ $order->patient->weight_kg }}</span> kg
                    &nbsp;&nbsp;&nbsp;
                    <span class="field-label">BSA:</span>
                    <span class="field-line" style="min-width:45px;">{{ $order->bsa }}</span> m²
                    @if ($order->crcl)
                        &nbsp;&nbsp;&nbsp;
                        <span class="field-label">CrCl:</span>
                        <span class="field-line" style="min-width:45px;">{{ $order->crcl }}</span> mL/min
                    @endif
                    &nbsp;&nbsp;&nbsp;
                    <span class="field-label">Cycle:</span>
                    <span class="field-line" style="min-width:30px;">{{ $order->cycle_number }}</span>
                    @if ($order->is_same_cycle)
                        <small> (same cycle)</small>
                    @endif
                </td>
            </tr>

            {{-- Row 5: Tests reminder (Before each cycle) --}}
            @if ($order->protocol->tests_reminder)
                <tr>
                    <td colspan="3" style="padding:4px 6px; border-bottom:1px solid #ccc;">
                        <span class="field-label">Before each cycle:</span> {{ $order->protocol->tests_reminder }}
                    </td>
                </tr>
            @endif

            {{-- Row 6: Dose modification --}}
            @if ($order->is_modified_protocol)
                <tr>
                    <td colspan="3" style="padding:4px 6px; background:#fff8e1;">
                        <span class="field-label">Dose modification for:</span>
                        <span class="cb"></span> Cardiology &nbsp;&nbsp;
                        <span class="cb cb-checked"></span> Other Toxicity
                        <span class="field-line"
                            style="min-width:160px;">{{ $order->dose_modification_reason ?? '' }}</span>
                    </td>
                </tr>
            @else
                <tr>
                    <td colspan="3" style="padding:4px 6px;">
                        <span class="field-label">Dose modification for:</span>
                        <span class="cb"></span> Cardiology &nbsp;&nbsp;
                        <span class="cb"></span> Other Toxicity
                        <span class="field-line" style="min-width:160px;"></span>
                    </td>
                </tr>
            @endif

        </table>
    </div>

    {{-- ═══════════════════════════════════════════════════════
     PRE-MEDICATIONS  (numbered narrative list)
═══════════════════════════════════════════════════════ --}}
    @php $preDrugs = $order->orderDrugs->where('category','pre_medication')->where('is_included',true)->values(); @endphp
    @if ($preDrugs->count())
        <div class="outer-box">
            <div class="section-header">Premedication 30 min before starting:</div>
            <ol class="med-list">
                @foreach ($preDrugs as $od)
                    <li>
                        <strong>{{ $od->drug->name }}</strong>
                        {{ number_format($od->final_dose, 2) }} {{ $od->drug->unit }}
                        @if ($od->protocolDrug->route)
                            {{ $od->protocolDrug->route }}
                        @endif
                        @if ($od->protocolDrug->frequency)
                            {{ $od->protocolDrug->frequency }}
                        @endif
                        @if ($od->protocolDrug->duration_days)
                            for {{ $od->protocolDrug->duration_days }} day(s)
                        @endif
                        @if ($od->protocolDrug->notes)
                            — {{ $od->protocolDrug->notes }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
     CHEMOTHERAPY  (narrative lines, matching reference)
═══════════════════════════════════════════════════════ --}}
    @php $chemoDrugs = $order->orderDrugs->where('category','chemotherapy')->where('is_included',true)->values(); @endphp
    @if ($chemoDrugs->count())
        <div class="outer-box">
            <div class="chemo-header">Chemotherapy</div>
            <div style="padding:2px 0 6px 0;">
                @foreach ($chemoDrugs as $od)
                    @php
                        $pd = $od->protocolDrug;
                        // Dose-per-unit label
                        $basisLabel = match ($pd->dose_type) {
                            'bsa_based' => number_format($pd->dose_per_unit, 2) . ' mg/m2',
                            'weight_based' => number_format($pd->dose_per_unit, 2) . ' mg/kg',
                            'carboplatin_calvert' => 'AUC ' . $pd->target_auc,
                            'fixed' => null,
                            default => null,
                        };
                        // Modification %
                        $modPct =
                            $od->calculated_dose > 0 ? round(($od->final_dose / $od->calculated_dose) * 100) : 100;
                    @endphp
                    <div class="chemo-line">
                        {{-- Drug name + basis + mod% + calculated dose --}}
                        <strong>{{ $od->drug->name }}</strong>
                        @if ($basisLabel)
                            {{ $basisLabel }} ( <strong>{{ $modPct }} %</strong> ) =
                            <strong>{{ number_format($od->final_dose, 2) }}</strong> {{ $od->drug->unit }}
                        @else
                            {{-- fixed dose --}}
                            <strong>{{ number_format($od->final_dose, 2) }}</strong> {{ $od->drug->unit }}
                        @endif

                        {{-- Inline infusion / administration notes from protocol --}}
                        @if ($pd->notes)
                            {{ $pd->notes }}
                        @endif

                        {{-- Per-cycle cap note --}}
                        @if ($od->cap_applied && $pd->per_cycle_cap)
                            <em>(capped at {{ number_format($pd->per_cycle_cap, 2) }}
                                {{ $pd->per_cycle_cap_unit }})</em>
                        @endif

                        {{-- Lifetime cap note --}}
                        @if ($pd->lifetime_cap)
                            Not to exceed lifetime cumulative dose of
                            {{ number_format($pd->lifetime_cap, 2) }} {{ $pd->lifetime_cap_unit }}.
                        @endif

                        {{-- Manual override note --}}
                        @if ($od->is_manually_overridden && $od->override_reason)
                            <em style="color:#c2410c;">[Override: {{ $od->override_reason }}]</em>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
     POST-MEDICATIONS  (numbered narrative list)
═══════════════════════════════════════════════════════ --}}
    @php $postDrugs = $order->orderDrugs->where('category','post_medication')->where('is_included',true)->values(); @endphp
    @if ($postDrugs->count())
        <div class="outer-box">
            <div class="section-header">Post-Chemotherapy Medications:</div>
            <ol class="med-list">
                @foreach ($postDrugs as $od)
                    <li>
                        <strong>{{ $od->drug->name }}</strong>
                        {{ number_format($od->final_dose, 2) }} {{ $od->drug->unit }}
                        @if ($od->protocolDrug->route)
                            {{ $od->protocolDrug->route }}
                        @endif
                        @if ($od->protocolDrug->frequency)
                            {{ $od->protocolDrug->frequency }}
                        @endif
                        @if ($od->protocolDrug->duration_days)
                            for {{ $od->protocolDrug->duration_days }} day(s)
                        @endif
                        @if ($od->protocolDrug->notes)
                            — {{ $od->protocolDrug->notes }}
                        @endif
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- Excluded drugs --}}
    @php $excluded = $order->orderDrugs->where('is_included', false); @endphp
    @if ($excluded->count())
        <div style="border:1px solid #ccc; padding:3px 8px; margin-bottom:5px; font-size:10px; color:#666;">
            <strong>Not given this cycle:</strong> {{ $excluded->pluck('drug.name')->implode(', ') }}
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════
     SIGNATURE BLOCK
═══════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td class="sig-cell">
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:26px;">
                        Consultant Stamp / Signature
                    </div>
                    <div style="border-top:1px solid #000; padding-top:2px; font-size:10px;">
                        <strong>Date:</strong> {{ $order->ordered_at->format('d/m/Y') }}
                        &nbsp;&nbsp;&nbsp;
                        <strong>Time:</strong> {{ $order->ordered_at->format('H:i') }}
                    </div>
                    @if ($order->consultant_name)
                        <div style="font-size:10px; margin-top:2px;">{{ $order->consultant_name }}</div>
                    @endif
                </td>
                <td class="sig-cell">
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:26px;">
                        Pharmacist Stamp / Signature
                    </div>
                    <div style="border-top:1px solid #000; padding-top:2px; font-size:10px;">
                        <strong>Date:</strong> ___________
                        &nbsp;&nbsp;&nbsp;
                        <strong>Time:</strong> _______
                    </div>
                    @if ($order->pharmacist_name)
                        <div style="font-size:10px; margin-top:2px;">{{ $order->pharmacist_name }}</div>
                    @endif
                </td>
                <td class="sig-cell">
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase; margin-bottom:26px;">
                        Noted By (Nurse) Stamp / Signature
                    </div>
                    <div style="border-top:1px solid #000; padding-top:2px; font-size:10px;">
                        <strong>Date:</strong> ___________
                        &nbsp;&nbsp;&nbsp;
                        <strong>Time:</strong> _______
                    </div>
                    @if ($order->nurse_name)
                        <div style="font-size:10px; margin-top:2px;">{{ $order->nurse_name }}</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div
        style="text-align:left; font-size:9px; color:#555; margin-top:4px; display:flex; justify-content:space-between;">
        <span>Chemotherapy Protocol page 1 of 1</span>
        <span>{{ $order->order_number }} &nbsp;|&nbsp; Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
            {{ env('HOSPITAL_NAME') }} &nbsp;|&nbsp; CONFIDENTIAL</span>
    </div>

</body>

</html>
