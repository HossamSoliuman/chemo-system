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

        /* ── Checkbox helper ── */
        .cb {
            display: inline-block;
            width: 11px;
            height: 11px;
            border: 1px solid #000;
            vertical-align: middle;
            margin-right: 2px;
            line-height: 11px;
            text-align: center;
            font-size: 9px;
        }

        .cb-checked::after {
            content: "✓";
            font-weight: bold;
            font-size: 9px;
        }

        /* ── Underline field ── */
        .uline {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 90px;
        }

        /* ── Outer bordered box ── */
        .outer-box {
            border: 1px solid #000;
            margin-bottom: 4px;
        }

        /* ── Section title (black bar) ── */
        .section-bar {
            font-weight: bold;
            font-size: 11px;
            padding: 2px 6px;
            border-bottom: 1px solid #000;
        }

        /* ── Drug prose paragraph ── */
        .drug-para {
            margin: 3px 0;
            line-height: 1.55;
            font-size: 11px;
        }

        .drug-dose-box {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 55px;
            text-align: center;
            font-weight: bold;
        }

        /* ── Modified banner ── */
        .modified-banner {
            border: 1.5px solid #c00;
            background: #fff0f0;
            padding: 4px 8px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── Signature table ── */
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

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 9px;
            color: #444;
            margin-top: 5px;
        }
    </style>
</head>

<body>

    {{-- ── PRINT / BACK BUTTONS ── --}}
    <div class="no-print" style="margin-bottom:10px; display:flex; gap:8px;">
        <button onclick="window.print()"
            style="background:#2563eb;color:#fff;border:none;padding:7px 18px;border-radius:6px;cursor:pointer;font-size:13px;">&#128438;
            Print</button>
        <a href="{{ route('orders.show', $order) }}"
            style="background:#f3f4f6;color:#374151;border:1px solid #d1d5db;padding:7px 18px;border-radius:6px;text-decoration:none;font-size:13px;">&#8592;
            Back</a>
    </div>

    {{-- ── MODIFIED PROTOCOL BANNER ── --}}
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

    {{-- ══════════════════════════════════════════════════════════════
     HEADER — logo left | patient ID right
═══════════════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        <table>
            <tr>
                {{-- Left: hospital branding --}}
                <td style="width:36%; border-right:1px solid #000; padding:8px 10px; vertical-align:middle;">
                    <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                        <img src="/health-saudi-logo.jpg" alt="Ministry of Health Logo"
                            style="width:40px; height:40px; object-fit:contain;">
                        <div>
                            <div style="font-size:9px; font-weight:bold; line-height:1.4;">Kingdom Of Saudi Arabia</div>
                            <div style="font-size:9px; line-height:1.4;">Ministry Of Health</div>
                            <div style="font-size:9px; line-height:1.4;">
                                {{ env('HOSPITAL_DEPT', 'Directorate Of Health Affairs') }}</div>
                        </div>
                    </div>
                    <div style="font-size:15px; font-weight:bold; margin-top:4px;">CHEMOTHERAPY PROTOCOL</div>
                    <div style="font-size:12px; font-family:'Arial'; direction:rtl; text-align:right; margin-top:2px;">
                        بروتوكول العلاج الكيميائي</div>
                </td>
                {{-- Right: patient fields --}}
                <td style="padding:6px 10px; vertical-align:top;">
                    <table style="width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Medical Record Number</strong></td>
                            <td style="padding:3px 4px;"><span class="uline"
                                    style="min-width:140px;">{{ $order->patient->mrn }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Name:</strong></td>
                            <td style="padding:3px 4px;"><span class="uline"
                                    style="min-width:200px;">{{ $order->patient->name }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Age:</strong></td>
                            <td style="padding:3px 4px;">
                                <span class="uline"
                                    style="min-width:45px;">{{ $order->patient->age }}</span>&nbsp;yrs/mos/day(s)
                                &nbsp;&nbsp;
                                <strong>Sex</strong>&nbsp;
                                {{-- Male checkbox --}}
                                <span class="cb {{ $order->patient->gender === 'male' ? 'cb-checked' : '' }}"></span>M
                                &nbsp;
                                {{-- Female checkbox --}}
                                <span class="cb {{ $order->patient->gender === 'female' ? 'cb-checked' : '' }}"></span>F
                            </td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Nationality</strong></td>
                            <td style="padding:3px 4px;"><span class="uline"
                                    style="min-width:180px;">{{ $order->patient->nationality ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Consultant In-charge</strong></td>
                            <td style="padding:3px 4px;"><span class="uline"
                                    style="min-width:180px;">{{ $order->consultant_name ?? '' }}</span></td>
                        </tr>
                        <tr>
                            <td style="padding:3px 0; white-space:nowrap;"><strong>Dept/Unit</strong></td>
                            <td style="padding:3px 4px;">
                                <span class="uline" style="min-width:100px;">&nbsp;</span>
                                &nbsp; <strong>Room/Bed No.</strong>
                                <span class="uline" style="min-width:70px;">&nbsp;</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
     CLINICAL INFO BLOCK
═══════════════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        {{-- Row 1: Pregnant / Lactating / Allergy --}}
        <div style="padding:4px 6px; border-bottom:1px solid #000;">
            <strong>Pregnant:</strong>&nbsp;
            <span class="cb {{ ($order->patient->pregnant ?? '') === 'yes' ? 'cb-checked' : '' }}"></span>Yes &nbsp;
            <span class="cb {{ ($order->patient->pregnant ?? '') === 'no' ? 'cb-checked' : '' }}"></span>No
            &nbsp;&nbsp;&nbsp;
            <strong>Lactating:</strong>&nbsp;
            <span class="cb {{ ($order->patient->lactating ?? '') === 'yes' ? 'cb-checked' : '' }}"></span>Yes &nbsp;
            <span class="cb {{ ($order->patient->lactating ?? '') === 'no' ? 'cb-checked' : '' }}"></span>No
            &nbsp;&nbsp;&nbsp;
            <strong>Allergy:</strong>&nbsp;
            <span class="cb {{ $order->patient->has_allergy ? 'cb-checked' : '' }}"></span>Yes &nbsp;
            <span class="cb {{ !$order->patient->has_allergy ? 'cb-checked' : '' }}"></span>No
            &nbsp;&nbsp;
            <strong>If yes Specify</strong>&nbsp;
            <span class="uline"
                style="min-width:160px;">{{ $order->patient->has_allergy ? $order->patient->allergy_details ?? '' : '' }}</span>
        </div>

        {{-- Row 2: Diagnosis / Stage / ECOG --}}
        <div style="padding:4px 6px; border-bottom:1px solid #000; display:flex; gap:12px; align-items:baseline;">
            <span><strong>Diagnosis:</strong> <span class="uline"
                    style="min-width:160px;">{{ $order->protocol->diagnosis->name ?? '' }}</span></span>
            <span><strong>Stage:</strong> <span class="uline"
                    style="min-width:60px;">{{ $order->patient->cancer_stage ?? '' }}</span></span>
            <span><strong>ECOG=</strong><span class="uline"
                    style="min-width:40px;">{{ $order->patient->ecog_status ?? '' }}</span></span>
        </div>

        {{-- Row 3: Protocol / Setting --}}
        <div style="padding:4px 6px; border-bottom:1px solid #000; display:flex; gap:20px; align-items:baseline;">
            <span style="flex:1;">
                <strong>Protocol:</strong>
                <span class="uline" style="min-width:140px;">{{ $order->protocol->name }}</span>
                @if ($order->is_split_cycle && $order->cycle_day_week)
                    &nbsp;<span
                        style="border:1px solid #1976d2;color:#1976d2;padding:1px 5px;font-size:10px;font-weight:bold;">{{ $order->cycle_day_week }}</span>
                @endif
            </span>
            <span><strong>SETTING OF CHEMOTHERAPY:</strong> <span class="uline"
                    style="min-width:90px;">{{ $order->patient->chemo_setting ?? '' }}</span></span>
        </div>

        {{-- Row 4: Ht / Wt / BSA / Cycle / Date --}}
        <div style="padding:4px 6px; border-bottom:1px solid #000;">
            <strong>Ht:</strong> <span class="uline" style="min-width:50px;">{{ $order->patient->height_cm }}</span>
            cm
            &nbsp;&nbsp;
            <strong>Wt:</strong> <span class="uline" style="min-width:50px;">{{ $order->patient->weight_kg }}</span>
            kg
            &nbsp;&nbsp;
            <strong>BSA:</strong> <span class="uline" style="min-width:50px;">{{ $order->bsa }}</span> m²
            &nbsp;&nbsp;
            <strong>CrCl:</strong> <span class="uline" style="min-width:50px;">{{ $order->crcl }}</span> mL/min
            &nbsp;&nbsp;
            <strong>Cycle:</strong> <span class="uline" style="min-width:40px;">{{ $order->cycle_number }}</span>
            @if ($order->is_same_cycle)
                <small>(same cycle)</small>
            @endif
            &nbsp;&nbsp;
            <strong>Date:</strong> {{ $order->ordered_at->format('d/m/Y') }}
        </div>

        {{-- Row 5: Before each cycle note --}}
        <div style="padding:4px 6px; border-bottom:1px solid #000; font-size:10.5px;">
            <strong>Before each cycle:</strong> CBC &amp; diff, platelets, creatinine &amp; LFT<br>
            Echo at base line, to be repeated after cycle 6
        </div>

        {{-- Row 6: Dose modification --}}
        @if ($order->is_modified_protocol)
            <div style="padding:4px 6px; background:#fff8e1;">
                <strong>Dose modification for:</strong>
                <span class="cb cb-checked"></span> Other Toxicity:
                <span class="uline" style="min-width:200px;">{{ $order->dose_modification_reason ?? '' }}</span>
            </div>
        @else
            <div style="padding:4px 6px;">
                <strong>Dose modification for:</strong>
                <span class="cb"></span> cardiology
                &nbsp;&nbsp;
                <span class="cb"></span> Other Toxicity
                <span class="uline" style="min-width:200px;"></span>
            </div>
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════════
     PREMEDICATION — numbered prose list (matches reference)
═══════════════════════════════════════════════════════════════ --}}
    @php $preDrugs = $order->orderDrugs->where('category', 'pre_medication')->where('is_included', true)->values(); @endphp
    @if ($preDrugs->count() > 0)
        <div class="outer-box">
            <div style="padding:4px 8px; border-bottom:1px solid #000;">
                <strong><u>Premedication 30 min before starting:</u></strong>
            </div>
            <div style="padding:5px 8px;">
                <ol style="margin:0; padding-left:22px;">
                    @foreach ($preDrugs as $od)
                        @php
                            $freq = $od->physician_frequency ?: $od->protocolDrug->frequency ?? '';
                            $route = $od->protocolDrug->route ?? '';
                            $dur =
                                $od->physician_duration ?:
                                ($od->protocolDrug->duration_days
                                    ? $od->protocolDrug->duration_days . ' day(s)'
                                    : '');
                            $note = $od->physician_note ?: $od->protocolDrug->notes ?? '';
                            $unit = $od->physician_dose_unit ?: $od->drug->unit;
                            $dose = number_format($od->final_dose, 2);
                        @endphp
                        <li style="margin-bottom:3px; line-height:1.5;">
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
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
     CHEMOTHERAPY — inline prose per drug (matches reference)
═══════════════════════════════════════════════════════════════ --}}
    @php $chemoDrugs = $order->orderDrugs->where('category', 'chemotherapy')->where('is_included', true)->values(); @endphp
    @if ($chemoDrugs->count() > 0)
        <div class="outer-box">
            <div class="section-bar">Chemotherapy</div>
            <div style="padding:5px 8px;">
                @foreach ($chemoDrugs as $od)
                    @php
                        $pd = $od->protocolDrug;
                        $doseLabel = match ($pd->dose_type ?? '') {
                            'bsa_based' => number_format($pd->dose_per_unit, 2) . ' mg/m²',
                            'weight_based' => number_format($pd->dose_per_unit, 2) . ' mg/kg',
                            'carboplatin_calvert' => 'AUC ' . $pd->target_auc,
                            'fixed' => 'Fixed ' . number_format($pd->dose_per_unit, 2) . ' mg',
                            default => '',
                        };

                        $modPct = '100%';
                        if ($od->calculated_dose > 0 && round($od->final_dose, 2) != round($od->calculated_dose, 2)) {
                            $modPct = round(($od->final_dose / $od->calculated_dose) * 100) . '%';
                        }

                        $effectiveUnit = $od->physician_dose_unit ?: $od->drug->unit;
                        $notes = $od->physician_note ?: $pd->notes ?? '';
                    @endphp

                    <p class="drug-para">
                        <strong>{{ $od->drug->name }}
                            @if ($doseLabel)
                                {{ $doseLabel }}
                            @endif
                            ({{ $modPct }})
                            =
                            <span class="drug-dose-box">{{ number_format($od->final_dose, 2) }}</span>
                            {{ $effectiveUnit }}
                        </strong>
                        @if ($notes)
                            {{ $notes }}
                        @endif
                        @if ($pd->per_cycle_cap)
                            <strong> Not to exceed {{ number_format($pd->per_cycle_cap, 2) }}
                                {{ $pd->per_cycle_cap_unit }} per cycle.</strong>
                        @endif
                        @if ($pd->lifetime_cap)
                            <strong> Not to exceed lifetime cumulative dose of
                                {{ number_format($pd->lifetime_cap, 2) }} {{ $pd->lifetime_cap_unit }}.</strong>
                        @endif
                        @if ($od->cap_applied)
                            <em style="color:#b45309;"> (cap applied)</em>
                        @endif
                        @if ($od->is_manually_overridden)
                            <em style="color:#c2410c;"> ✎
                                Override{{ $od->override_reason ? ': ' . $od->override_reason : '' }}</em>
                        @endif
                    </p>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
     POST-MEDICATIONS — numbered prose list
═══════════════════════════════════════════════════════════════ --}}
    @php $postDrugs = $order->orderDrugs->where('category', 'post_medication')->where('is_included', true)->values(); @endphp
    @if ($postDrugs->count() > 0)
        <div class="outer-box">
            <div style="padding:4px 8px; border-bottom:1px solid #000;">
                <strong><u>Post-Chemotherapy Medications:</u></strong>
            </div>
            <div style="padding:5px 8px;">
                <ol style="margin:0; padding-left:22px;">
                    @foreach ($postDrugs as $od)
                        @php
                            $freq = $od->physician_frequency ?: $od->protocolDrug->frequency ?? '';
                            $route = $od->protocolDrug->route ?? '';
                            $dur =
                                $od->physician_duration ?:
                                ($od->protocolDrug->duration_days
                                    ? $od->protocolDrug->duration_days . ' day(s)'
                                    : '');
                            $note = $od->physician_note ?: $od->protocolDrug->notes ?? '';
                            $unit = $od->physician_dose_unit ?: $od->drug->unit;
                            $dose = number_format($od->final_dose, 2);
                        @endphp
                        <li style="margin-bottom:3px; line-height:1.5;">
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
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
     EXCLUDED DRUGS NOTE
═══════════════════════════════════════════════════════════════ --}}
    @php $excluded = $order->orderDrugs->where('is_included', false); @endphp
    @if ($excluded->count() > 0)
        <div style="border:1px solid #999; padding:3px 8px; margin-bottom:4px; font-size:10px; color:#555;">
            <strong>Excluded from this order:</strong> {{ $excluded->pluck('drug.name')->implode(', ') }}
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
     SIGNATURE BLOCK — 3 columns (matches reference exactly)
═══════════════════════════════════════════════════════════════ --}}
    <div class="outer-box">
        <table style="width:100%; border-collapse:collapse;">
            <tr>
                <td class="sig-td">
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">Consultant Stamp
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
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">Pharmacist Stamp
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
                    <div style="font-weight:bold; font-size:10px; text-transform:uppercase;">Noted By (Nurse) Stamp
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

    {{-- ── FOOTER ── --}}
    <div class="footer">
        Chemotherapy Protocol page 1 of 1 &nbsp;|&nbsp;
        {{ $order->order_number }} &nbsp;|&nbsp;
        Printed: {{ now()->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        {{ env('HOSPITAL_NAME', 'General Oncology Center') }}
    </div>

</body>

</html>
