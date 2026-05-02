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
        }

        .no-print {
            display: block;
            padding: 10px 14px;
            background: #f3f4f6;
            border-bottom: 1px solid #ddd;
        }

        @media print {
            @page {
                margin: 0.8cm 1cm;
                size: A4 portrait;
            }

            .no-print {
                display: none !important;
            }

            body {
                font-size: 11px;
            }
        }

        .page {
            padding: 0.4cm 0.5cm;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        .cb {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            vertical-align: middle;
            margin-right: 2px;
            font-size: 10px;
            font-weight: bold;
            line-height: 1;
        }

        .uline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 80px;
        }

        .outer-box {
            border: 1px solid #000;
            margin-bottom: 4px;
        }

        .section-bar {
            font-weight: bold;
            font-size: 11px;
            padding: 3px 7px;
            border-bottom: 1px solid #000;
        }

        .drug-para {
            margin: 4px 0;
            line-height: 1.6;
            font-size: 11px;
        }

        .drug-dose-box {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 55px;
            text-align: center;
            font-weight: bold;
        }

        .modified-banner {
            border: 1.5px solid #c00;
            background: #fff0f0;
            padding: 4px 8px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sig-td {
            border: 1px solid #000;
            padding: 8px 8px 5px;
            vertical-align: bottom;
            width: 33.33%;
        }

        .sig-line {
            border-top: 1px solid #000;
            margin-top: 22px;
            padding-top: 2px;
            font-size: 10px;
            display: flex;
            gap: 18px;
        }

        .footer {
            text-align: left;
            font-size: 9px;
            color: #444;
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    <div class="no-print" style="display:flex; gap:10px; align-items:center;">
        <button onclick="window.print()"
            style="background:#2563eb;color:#fff;border:none;padding:7px 20px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:bold;">&#128438;
            Print</button>
        <a href="{{ route('orders.show', $order) }}"
            style="background:#fff;color:#374151;border:1px solid #d1d5db;padding:7px 18px;border-radius:6px;text-decoration:none;font-size:13px;">&#8592;
            Back</a>
        <span style="color:#6b7280;font-size:12px;">{{ $order->order_number }} &nbsp;|&nbsp;
            {{ $order->ordered_at->format('d M Y H:i') }}</span>
    </div>

    <div class="page">

        @if ($order->is_modified_protocol)
            <div class="modified-banner">
                <span style="font-size:16px;">&#9888;</span>
                <div>
                    <strong style="color:#c00;font-size:11px;text-transform:uppercase;">Modified Protocol Order</strong>
                    @if ($order->dose_modification_reason)
                        <div style="font-size:10px;color:#c00;">Reason: {{ $order->dose_modification_reason }}</div>
                    @endif
                </div>
                <div style="margin-left:auto;font-size:12px;font-weight:bold;color:#c00;">Dose Modified Order</div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════
     HEADER — MOH logo left | patient fields right
═══════════════════════════════════════════════════════ --}}
        <div class="outer-box">
            <table>
                <tr>
                    <td style="width:36%; border-right:1px solid #000; padding:7px 10px; vertical-align:middle;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                            <img src="/health-saudi-logo.jpg" alt="MOH"
                                style="width:42px; height:42px; object-fit:contain;">
                            <div>
                                <div style="font-size:8.5px; font-weight:bold; line-height:1.5;">Kingdom Of Saudi Arabia
                                </div>
                                <div style="font-size:8.5px; line-height:1.5;">Ministry Of Health</div>
                                <div style="font-size:8.5px; line-height:1.5;">
                                    {{ env('HOSPITAL_DEPT', 'Directorate Of Health Affairs') }}</div>
                                <div style="font-size:8.5px; font-weight:bold; line-height:1.5;">
                                    {{ env('HOSPITAL_NAME', 'General Oncology Center') }}</div>
                            </div>
                        </div>
                        <div style="font-size:15px; font-weight:bold; margin-top:4px; line-height:1.3;">CHEMOTHERAPY
                            PROTOCOL</div>
                        <div style="font-size:12px; direction:rtl; text-align:right; margin-top:2px;">بروتوكول العلاج
                            الكيميائي</div>
                    </td>
                    <td style="padding:5px 10px; vertical-align:top;">
                        <table style="width:100%;">
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Medical Record Number</strong>
                                </td>
                                <td style="padding:2px 4px;"><span class="uline"
                                        style="min-width:140px;">{{ $order->patient->mrn }}</span></td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Name:</strong></td>
                                <td style="padding:2px 4px;"><span class="uline"
                                        style="min-width:200px;">{{ $order->patient->name }}</span></td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Age:</strong></td>
                                <td style="padding:2px 4px;">
                                    <span class="uline"
                                        style="min-width:40px;">{{ $order->patient->age }}</span>&nbsp;yrs/mos/day(s)
                                    &nbsp;&nbsp;<strong>Sex</strong>&nbsp;
                                    <span class="cb">{{ $order->patient->gender === 'male' ? '✓' : '' }}</span>M
                                    &nbsp;
                                    <span class="cb">{{ $order->patient->gender === 'female' ? '✓' : '' }}</span>F
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Nationality</strong></td>
                                <td style="padding:2px 4px;"><span class="uline"
                                        style="min-width:180px;">{{ $order->patient->nationality ?? '' }}</span></td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Consultant In-charge</strong>
                                </td>
                                <td style="padding:2px 4px;"><span class="uline"
                                        style="min-width:180px;">{{ $order->consultant_name ?? ($order->patient->consultant_in_charge ?? '') }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:2px 0; white-space:nowrap;"><strong>Dept/Unit</strong></td>
                                <td style="padding:2px 4px;">
                                    <span class="uline" style="min-width:90px;">&nbsp;</span>
                                    &nbsp;<strong>Room/Bed No.</strong>
                                    <span class="uline" style="min-width:70px;">&nbsp;</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

        {{-- ═══════════════════════════════════════════════════════
     CLINICAL INFO BLOCK
═══════════════════════════════════════════════════════ --}}
        <div class="outer-box">

            {{-- Pregnant / Lactating / Allergy --}}
            <div style="padding:3px 7px; border-bottom:1px solid #000; font-size:10.5px;">
                <strong>Pregnant:</strong>&nbsp;
                <span class="cb">{{ ($order->patient->pregnant ?? '') === 'yes' ? '✓' : '' }}</span>Yes &nbsp;
                <span class="cb">{{ ($order->patient->pregnant ?? '') === 'no' ? '✓' : '' }}</span>No
                &nbsp;&nbsp;&nbsp;
                <strong>Lactating:</strong>&nbsp;
                <span class="cb">{{ ($order->patient->lactating ?? '') === 'yes' ? '✓' : '' }}</span>Yes &nbsp;
                <span class="cb">{{ ($order->patient->lactating ?? '') === 'no' ? '✓' : '' }}</span>No
                &nbsp;&nbsp;&nbsp;
                <strong>Allergy:</strong>&nbsp;
                <span class="cb">{{ $order->patient->has_allergy ? '✓' : '' }}</span>Yes &nbsp;
                <span class="cb">{{ !$order->patient->has_allergy ? '✓' : '' }}</span>No
                &nbsp;&nbsp;
                <strong>If yes Specify</strong>&nbsp;
                <span class="uline"
                    style="min-width:150px;">{{ $order->patient->has_allergy ? $order->patient->allergy_details ?? '' : '' }}</span>
            </div>

            {{-- Diagnosis / Stage / ECOG --}}
            <div
                style="padding:3px 7px; border-bottom:1px solid #000; font-size:10.5px; display:flex; gap:12px; flex-wrap:wrap; align-items:baseline;">
                <span><strong>Diagnosis:</strong> <span class="uline"
                        style="min-width:160px;">{{ $order->protocol->diagnosis->name }}</span></span>
                <span><strong>Stage:</strong> <span class="uline"
                        style="min-width:55px;">{{ $order->patient->cancer_stage ?? '' }}</span></span>
                <span style="margin-left:auto;"><strong>ECOG :</strong> <span class="uline"
                        style="min-width:40px;">{{ $order->patient->ecog_status ?? '' }}</span></span>
            </div>

            {{-- Protocol / Setting --}}
            <div
                style="padding:3px 7px; border-bottom:1px solid #000; font-size:10.5px; display:flex; gap:12px; align-items:baseline;">
                <span style="flex:1;">
                    <strong>Protocol:</strong>
                    <span class="uline" style="min-width:140px;">{{ $order->protocol->name }}</span>
                    @if ($order->is_split_cycle && $order->cycle_day_week)
                        &nbsp;<span
                            style="border:1px solid #1976d2;color:#1976d2;padding:1px 5px;font-size:9px;font-weight:bold;">{{ $order->cycle_day_week }}</span>
                    @endif
                </span>
                <span style="white-space:nowrap;"><strong>SETTING OF CTHEMOTHERAPY:</strong> <span class="uline"
                        style="min-width:80px;">{{ $order->patient->chemo_setting ?? '' }}</span></span>
            </div>

            {{-- Ht / Wt / BSA / Cycle --}}
            <div
                style="padding:3px 7px; border-bottom:1px solid #000; font-size:10.5px; display:flex; gap:0; flex-wrap:nowrap;">
                <span style="margin-right:16px;"><strong>Ht:</strong> <span class="uline"
                        style="min-width:44px;">{{ $order->patient->height_cm }}</span> cm</span>
                <span style="margin-right:16px;"><strong>Wt:</strong> <span class="uline"
                        style="min-width:44px;">{{ $order->patient->weight_kg }}</span> kg</span>
                <span style="margin-right:16px;"><strong>BSA:</strong> <span class="uline"
                        style="min-width:44px;">{{ $order->bsa }}</span> m²</span>
                <span style="margin-left:auto; margin-right:16px;"><strong>CYCLE:</strong> <span class="uline"
                        style="min-width:35px;">{{ $order->cycle_number }}</span>
                    @if ($order->is_same_cycle)
                        <small>(same)</small>
                    @endif
                </span>
            </div>

            {{-- Before each cycle (from protocol tests_reminder) --}}
            @php $testsText = $order->protocol->tests_reminder; @endphp
            <div style="padding:3px 7px; border-bottom:1px solid #000; font-size:10.5px;">
                <strong>Before each cycle:</strong> {{ $testsText ?: 'CBC & diff, platelets, creatinine & LFT' }}
            </div>

            {{-- Dose modification --}}
            @if ($order->is_modified_protocol)
                <div style="padding:3px 7px; background:#fff8e1; font-size:10.5px;">
                    <strong>Dose modification for:</strong>
                    <span class="cb">✓</span> Other Toxicity:
                    <span class="uline" style="min-width:180px;">{{ $order->dose_modification_reason ?? '' }}</span>
                </div>
            @else
                <div style="padding:3px 7px; font-size:10.5px;">
                    <strong>Dose modification for:</strong>
                    <span class="cb"></span> Hematology
                    &nbsp;&nbsp;&nbsp;
                    <span class="cb"></span> Other Toxicity <span class="uline" style="min-width:200px;"></span>
                </div>
            @endif

        </div>

        {{-- ═══════════════════════════════════════════════════════
     PREMEDICATION — numbered prose list
═══════════════════════════════════════════════════════ --}}
        @php $preDrugs = $order->orderDrugs->where('category','pre_medication')->where('is_included',true)->values(); @endphp
        @if ($preDrugs->count() > 0)
            <div class="outer-box">
                <div style="padding:3px 8px; border-bottom:1px solid #000;">
                    <strong><u>Premedication 30 min before starting:</u></strong>
                </div>
                <div style="padding:4px 8px;">
                    <ol style="margin:0; padding-left:22px;">
                        @foreach ($preDrugs as $od)
                            @php
                                $unit = $od->physician_dose_unit ?: $od->drug->unit;
                                $dose = number_format($od->final_dose, 2);
                                $route = $od->protocolDrug->route ?? '';
                                $freq = $od->physician_frequency ?: $od->protocolDrug->frequency ?? '';
                                $dur =
                                    $od->physician_duration ?:
                                    ($od->protocolDrug->duration_days
                                        ? $od->protocolDrug->duration_days . ' day(s)'
                                        : '');
                                $note = $od->physician_note ?: $od->protocolDrug->notes ?? '';
                            @endphp
                            <li style="margin-bottom:3px; line-height:1.55; font-size:11px;">
                                <strong>{{ $od->drug->name }}</strong>
                                {{ $dose }} {{ $unit }}
                                @if ($route)
                                    {{ $route }}
                                @endif
                                @if ($freq)
                                    {{ $freq }}
                                @endif
                                @if ($dur)
                                    for {{ $dur }}
                                @endif
                                @if ($note)
                                    — <em>{{ $note }}</em>
                                @endif
                                @if ($od->is_manually_overridden && $od->override_reason)
                                    <span style="color:#c00; font-size:9.5px;"> [Override:
                                        {{ $od->override_reason }}]</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════
     CHEMOTHERAPY — inline prose (DrugName Xmg/m² (100%) = ____ mg instructions)
═══════════════════════════════════════════════════════ --}}
        @php $chemoDrugs = $order->orderDrugs->where('category','chemotherapy')->where('is_included',true)->values(); @endphp
        @if ($chemoDrugs->count() > 0)
            <div class="outer-box">
                <div class="section-bar">Chemotherapy :</div>
                <div style="padding:5px 8px;">
                    @foreach ($chemoDrugs as $od)
                        @php
                            $pd = $od->protocolDrug;

                            $doseLabel =
                                $pd->dose_label ?:
                                match ($pd->dose_type ?? '') {
                                    'bsa_based' => number_format($pd->dose_per_unit, 2) . ' mg/m²',
                                    'weight_based' => number_format($pd->dose_per_unit, 2) . ' mg/kg',
                                    'crcl_based' => number_format($pd->dose_per_unit, 2) . ' mg/mL/min',
                                    'carboplatin_calvert' => 'AUC ' . $pd->target_auc,
                                    'fixed' => '',
                                    default => '',
                                };

                            $modPct = '100';
                            if (
                                $od->calculated_dose > 0 &&
                                round($od->final_dose, 2) != round($od->calculated_dose, 2)
                            ) {
                                $modPct = round(($od->final_dose / $od->calculated_dose) * 100);
                            }
                            $isModified = $modPct != '100';

                            $effectiveUnit = $od->physician_dose_unit ?: $od->drug->unit;
                            $adminNote = $od->physician_note ?: $pd->notes ?? '';
                        @endphp
                        <p class="drug-para">
                            <strong>
                                {{ strtoupper($od->drug->name) }}
                                @if ($doseLabel)
                                    {{ $doseLabel }}
                                @endif
                                (<span style="{{ $isModified ? 'color:#c00;' : '' }}">{{ $modPct }} %</span>)
                                =
                                <span class="drug-dose-box">{{ number_format($od->final_dose, 2) }}</span>
                                {{ $effectiveUnit }}
                            </strong>
                            @if ($adminNote)
                                {{ $adminNote }}
                            @endif
                            @if ($pd->per_cycle_cap)
                                <strong>Not to exceed {{ number_format($pd->per_cycle_cap, 2) }}
                                    {{ $pd->per_cycle_cap_unit }} per cycle.</strong>
                            @endif
                            @if ($pd->lifetime_cap)
                                <strong>Not to exceed lifetime cumulative dose of
                                    {{ number_format($pd->lifetime_cap, 2) }} {{ $pd->lifetime_cap_unit }}.</strong>
                            @endif
                            @if ($od->cap_applied)
                                <em style="color:#b45309;"> (cap applied)</em>
                            @endif
                            @if ($od->is_manually_overridden)
                                <em style="color:#c2410c;"> &#9998;
                                    Override{{ $od->override_reason ? ': ' . $od->override_reason : '' }}</em>
                            @endif
                        </p>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════
     POST-MEDICATIONS — numbered prose list
═══════════════════════════════════════════════════════ --}}
        @php $postDrugs = $order->orderDrugs->where('category','post_medication')->where('is_included',true)->values(); @endphp
        @if ($postDrugs->count() > 0)
            <div class="outer-box">
                <div style="padding:3px 8px; border-bottom:1px solid #000;">
                    <strong><u>Post-Chemotherapy Medications:</u></strong>
                </div>
                <div style="padding:4px 8px;">
                    <ol style="margin:0; padding-left:22px;">
                        @foreach ($postDrugs as $od)
                            @php
                                $unit = $od->physician_dose_unit ?: $od->drug->unit;
                                $dose = number_format($od->final_dose, 2);
                                $route = $od->protocolDrug->route ?? '';
                                $freq = $od->physician_frequency ?: $od->protocolDrug->frequency ?? '';
                                $dur =
                                    $od->physician_duration ?:
                                    ($od->protocolDrug->duration_days
                                        ? $od->protocolDrug->duration_days . ' day(s)'
                                        : '');
                                $note = $od->physician_note ?: $od->protocolDrug->notes ?? '';
                            @endphp
                            <li style="margin-bottom:3px; line-height:1.55; font-size:11px;">
                                <strong>{{ $od->drug->name }}</strong>
                                {{ $dose }} {{ $unit }}
                                @if ($route)
                                    {{ $route }}
                                @endif
                                @if ($freq)
                                    {{ $freq }}
                                @endif
                                @if ($dur)
                                    for {{ $dur }}
                                @endif
                                @if ($note)
                                    — <em>{{ $note }}</em>
                                @endif
                                @if ($od->is_manually_overridden && $od->override_reason)
                                    <span style="color:#c00; font-size:9.5px;"> [Override:
                                        {{ $od->override_reason }}]</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        @endif

        {{-- Excluded drugs --}}
        @php $excluded = $order->orderDrugs->where('is_included', false); @endphp
        @if ($excluded->count() > 0)
            <div style="border:1px solid #999; padding:3px 8px; margin-bottom:4px; font-size:10px; color:#555;">
                <strong>Excluded from this order:</strong> {{ $excluded->pluck('drug.name')->implode(', ') }}
            </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════
     SIGNATURE BLOCK — 3 column bordered table
═══════════════════════════════════════════════════════ --}}
        <div class="outer-box">
            <table style="width:100%;">
                <tr>
                    <td class="sig-td">
                        <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">CONSULTANT Stamp
                            /Signature</div>
                        <div class="sig-line">
                            <span><strong>Date:</strong> {{ $order->ordered_at->format('d/m/Y') }}</span>
                            <span><strong>Time:</strong> {{ $order->ordered_at->format('H:i') }}</span>
                        </div>
                        @if ($order->consultant_name)
                            <div style="font-size:10px; margin-top:2px;">{{ $order->consultant_name }}</div>
                        @endif
                    </td>
                    <td class="sig-td" style="border-left:1px solid #000;">
                        <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">PHARMACIST Stamp
                            /Signature</div>
                        <div class="sig-line">
                            <span><strong>Date:</strong> ___________</span>
                            <span><strong>Time:</strong> _______</span>
                        </div>
                        @if ($order->pharmacist_name)
                            <div style="font-size:10px; margin-top:2px;">{{ $order->pharmacist_name }}</div>
                        @endif
                    </td>
                    <td class="sig-td" style="border-left:1px solid #000;">
                        <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">NOTED BY (NURSE) Stamp
                            /Signature</div>
                        <div class="sig-line">
                            <span><strong>Date:</strong> ___________</span>
                            <span><strong>Time:</strong> _______</span>
                        </div>
                        @if ($order->nurse_name)
                            <div style="font-size:10px; margin-top:2px;">{{ $order->nurse_name }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="footer">
            <span>Chemotherapy Protocol page 1 of 1</span>
            <span>{{ env('HOSPITAL_CODE', 'KAASH') }} 07/011-00</span>
        </div>

    </div>{{-- end .page --}}
</body>

</html>
