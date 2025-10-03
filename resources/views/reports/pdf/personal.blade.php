<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <style>
        /* (Todo o seu CSS que já corrigimos antes continua aqui, sem alterações) */
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 9px;
            color: #333;
            margin: 20px;
        }

        .report-header {
            width: 100%;
            border-bottom: 1px solid #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-text {
            text-align: center;
        }

        .header-text h4 {
            margin: 0;
            font-size: 11px;
            font-weight: bold;
        }

        .header-text h5 {
            margin: 0;
            font-size: 9px;
            font-weight: normal;
        }

        .header-text hr {
            border: none;
            border-top: 0.5px solid #000;
            margin: 3px 0;
        }

        .logo-ifnmg {
            width: 55px;
        }

        .logo-siga {
            width: 90px;
        }

        .summary-section {
            width: 100%;
            margin-bottom: 25px;
            font-size: 11px;
            border-left: 4px solid #4a90e2;
            padding-left: 15px;
            background-color: #f8fafc;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .summary-section td {
            padding: 3px 0;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead {
            display: table-header-group;
        }

        .main-table th {
            border: 1px solid #ccc;
            padding: 8px 6px;
            font-size: 8px;
            text-transform: uppercase;
            background: #f1f5f9;
            font-weight: 700;
            text-align: left;
        }

        .main-table td {
            border: 1px solid #ccc;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        .main-table tbody tr {
            page-break-inside: avoid;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .signature-section {
            width: 280px;
            margin-top: 60px;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 8px;
            font-size: 10px;
        }

        .not-available {
            color: #888;
            font-size: 8.5px;
        }

        .no-break {
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica");
            $pdf->page_text($pdf->get_width() - 85, $pdf->get_height() - 35, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 8);
            $pdf->page_text(25, $pdf->get_height() - 35, "SIGA-IF - Relatório emitido em {{ now()->format('d/m/Y H:i') }}", $font, 8);
        }
    </script>

    <div class="report-header">
        <table class="header-table">
            <tr>
                <td style="text-align: left; width: 15%;"><img src="{{ public_path('images/logo-ifnmg.png') }}"
                        alt="Logo IFNMG" class="logo-ifnmg"></td>
                <td class="header-text" style="width: 70%;">
                    <h4>MINISTÉRIO DA EDUCAÇÃO</h4>
                    <h5>SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</h5>
                    <h5>INSTITUTO FEDERAL DO NORTE DE MINAS GERAIS – CAMPUS ALMENARA</h5>
                    <hr>
                    <h4>{{ $title }}</h4>
                </td>
                <td style="text-align: right; width: 15%;"><img src="{{ public_path('images/logo-siga.png') }}"
                        alt="Logo SIGA" class="logo-siga"></td>
            </tr>
        </table>
    </div>

    <table class="summary-section">
        @if ($reportType === 'vehicle')
            <tr>
                <td><strong>Veículo:</strong> {{ $vehicleDescription }}</td>
            </tr>
            <tr>
                <td><strong>Período de Apuração:</strong> {{ $period }}</td>
            </tr>
        @else
            {{-- Por padrão, assume o relatório pessoal --}}
            <tr>
                <td><strong>Porteiro:</strong> {{ $porteiroName }}</td>
            </tr>
            <tr>
                <td><strong>Período de Apuração:</strong> {{ $period }}</td>
            </tr>
        @endif
    </table>

    <table class="main-table">
        <colgroup>
            @if ($reportType === 'vehicle')
                <col style="width:25%" />
                <col style="width:25%" />
                <col style="width:25%" />
                <col style="width:25%" />
            @else
                <col style="width:25%" />
                <col style="width:20%" />
                <col style="width:20%" />
                <col style="width:25%" />
                <col style="width:10%" />
            @endif
        </colgroup>
        <thead>
            <tr>
                @if ($reportType === 'vehicle')
                    <th colspan="4"
                        style="background:#fff; border:none; font-size:12px; font-weight:bold; color:#1e2B3B; padding:10px 0;">
                        Registos de Utilização</th>
                @else
                    <th colspan="5"
                        style="background:#fff; border:none; font-size:12px; font-weight:bold; color:#1e2B3B; padding:10px 0;">
                        Registos de Veículos Particulares</th>
                @endif
            </tr>
            <tr>
                @if ($reportType === 'vehicle')
                    <th>Condutor</th>
                    <th>Entrada</th>
                    <th>Saída</th>
                    <th>Motivo</th>
                @else
                    <th>Veículo</th>
                    <th>Condutor</th>
                    <th>Período (Entrada / Saída)</th>
                    <th>Motivo</th>
                    <th>Porteiro (Saída)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $entry)
                <tr>
                    @if ($reportType === 'vehicle')
                        <td>{{ $entry->driver?->name ?? 'Não informado' }}</td>
                        <td><span
                                class="no-break">{{ $entry->entry_at ? $entry->entry_at->format('d/m/y H:i') : 'N/A' }}</span>
                        </td>
                        <td><span
                                class="no-break">{{ $entry->exit_at ? $entry->exit_at->format('d/m/y H:i') : '(No pátio)' }}</span>
                        </td>
                        <td>{{ $entry->entry_reason }}</td>
                    @else
                        <td>{{ $entry->vehicle?->model ?? $entry->vehicle_model }}<br><small
                                class="not-available no-break">{!! str_replace('-', '&#8209;', $entry->vehicle?->license_plate ?? $entry->vehicle_plate) !!}</small></td>
                        <td>{{ $entry->driver?->name ?? 'Não informado' }}</td>
                        <td>
                            <span
                                class="no-break">{{ $entry->entry_at ? $entry->entry_at->format('d/m/y H:i') : 'N/A' }}</span><br>
                            <span
                                class="no-break">{{ $entry->exit_at ? $entry->exit_at->format('d/m/y H:i') : '(No pátio)' }}</span>
                        </td>
                        <td>{{ $entry->entry_reason }}</td>
                        <td class="no-break">{{ $entry->guard_on_exit ?? 'N/A' }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $reportType === 'vehicle' ? 4 : 5 }}" class="text-center" style="padding: 20px;">
                        Nenhum registo encontrado para este período.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="signature-section">
        <tr>
            @if ($reportType === 'vehicle')
                <td class="signature-line">
                    <strong>Responsável pelo Relatório</strong>
                </td>
            @else
                <td class="signature-line">
                    {{ $porteiroName }}<br>
                    <strong>Porteiro Responsável</strong>
                </td>
            @endif
        </tr>
    </table>
</body>

</html>
