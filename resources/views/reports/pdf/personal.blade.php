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
            /* Ajustado */
        }

        .logo-siga {
            width: 90px;
        }

        .summary-section {
            width: 100%;
            margin-bottom: 15px;
            /* Reduzido */
            font-size: 10px;
            /* Levemente maior */
            border-left: 3px solid #4a90e2;
            /* Mais fino */
            padding-left: 10px;
            background-color: #f8fafc;
            padding-top: 8px;
            padding-bottom: 8px;
        }

        .summary-section td {
            padding: 2px 0;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            /* Ajuda a controlar largura das colunas */
        }

        thead {
            display: table-header-group;
            /* Repete cabeçalho */
        }

        .main-table th {
            border: 1px solid #ccc;
            padding: 6px 4px;
            /* Ajustado padding */
            font-size: 8px;
            text-transform: uppercase;
            background: #f1f5f9;
            font-weight: 700;
            text-align: left;
            vertical-align: middle;
            /* Alinha melhor o texto do cabeçalho */
        }

        .main-table td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            /* Ajustado padding */
            vertical-align: top;
            word-wrap: break-word;
            /* Quebra palavras longas */
        }

        .main-table tbody tr {
            page-break-inside: avoid;
        }

        .main-table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .signature-section {
            width: 250px;
            /* Reduzido */
            margin-top: 40px;
            /* Reduzido */
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            /* Reduzido */
            font-size: 9px;
            /* Ajustado */
        }

        .not-available {
            color: #888;
            font-style: italic;
            /* Adicionado */
            font-size: 8px;
            /* Reduzido */
        }

        .no-break {
            white-space: nowrap;
        }

        .text-center {
            text-align: center;
        }

        /* Footer styles */
        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 8px;
            border-top: 0.5px solid #ccc;
            /* Mais fino */
            padding-top: 5px;
            margin: 0 20px;
            /* Alinha com margens */
        }

        .footer .page-number {
            float: right;
        }

        .footer .generation-date {
            float: left;
        }
    </style>
</head>

<body>
    {{-- Script PHP para número de página e data no rodapé --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Helvetica", "normal");
            $size = 8;
            $pageWidth = $pdf->get_width();
            $pageHeight = $pdf->get_height();
            $x_page = $pageWidth - 85; // Posição X número página
            $x_date = 20; // Posição X data
            $y = $pageHeight - 35; // Posição Y comum

            // Número da página
            $pdf->page_text($x_page, $y, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, $size);
            // Data de geração
            $pdf->page_text($x_date, $y, "SIGA-IF - Relatório emitido em {{ now()->format('d/m/Y H:i') }}", $font, $size);
        }
    </script>

    {{-- Cabeçalho do Relatório --}}
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

    {{-- Seção de Resumo/Filtros --}}
    <table class="summary-section">
        @if ($reportType === 'vehicle')
            <tr>
                <td><strong>Veículo:</strong> {{ $vehicleDescription }}</td>
            </tr>
            <tr>
                <td><strong>Período de Apuração:</strong> {{ $period }}</td>
            </tr>
        @else
            {{-- Relatório Pessoal (private ou official) --}}
            <tr>
                <td><strong>Porteiro:</strong> {{ $porteiroName }}</td>
            </tr>
            <tr>
                <td><strong>Período de Apuração:</strong> {{ $period }}</td>
            </tr>
        @endif
    </table>

    {{-- Tabela Principal --}}
    <table class="main-table">
        <colgroup>
            {{-- Define larguras das colunas baseadas no tipo de relatório --}}
            @if ($reportType === 'vehicle')
                {{-- Relatório por Veículo Oficial --}}
                <col style="width: 20%;" /> {{-- Condutor --}}
                <col style="width: 20%;" /> {{-- Partida (Data/Hora) --}}
                <col style="width: 20%;" /> {{-- Chegada (Data/Hora) --}}
                <col style="width: 25%;" /> {{-- Destino/Motivo --}}
                <col style="width: 15%;" /> {{-- Porteiro Chegada --}}
            @elseif ($reportType === 'official')
                {{-- Relatório Pessoal Oficial --}}
                <col style="width: 20%;" /> {{-- Veículo --}}
                <col style="width: 20%;" /> {{-- Condutor --}}
                <col style="width: 15%;" /> {{-- Partida --}}
                <col style="width: 15%;" /> {{-- Chegada --}}
                <col style="width: 20%;" /> {{-- Destino --}}
                <col style="width: 10%;" /> {{-- Porteiro Partida --}}
            @else
                {{-- Relatório Pessoal Particular --}}
                <col style="width: 25%;" /> {{-- Veículo --}}
                <col style="width: 20%;" /> {{-- Condutor --}}
                <col style="width: 15%;" /> {{-- Entrada --}}
                <col style="width: 15%;" /> {{-- Saída --}}
                <col style="width: 15%;" /> {{-- Motivo --}}
                <col style="width: 10%;" /> {{-- Porteiro Saída --}}
            @endif
        </colgroup>
        <thead>
            {{-- Cabeçalho Principal da Tabela --}}
            <tr>
                @php
                    $colspan = $reportType === 'vehicle' ? 5 : ($reportType === 'official' ? 6 : 6);
                    $tableTitle =
                        $reportType === 'vehicle'
                            ? 'Registos de Utilização'
                            : ($reportType === 'official'
                                ? 'Registos de Viagens Oficiais'
                                : 'Registos de Veículos Particulares');
                @endphp
                <th colspan="{{ $colspan }}"
                    style="background:#fff; border:none; font-size:11px; font-weight:bold; color:#1e2B3B; padding:8px 0;">
                    {{ $tableTitle }}
                </th>
            </tr>
            {{-- Cabeçalhos das Colunas --}}
            <tr>
                @if ($reportType === 'vehicle')
                    <th>Condutor</th>
                    <th>Partida (Data/Hora)</th>
                    <th>Chegada (Data/Hora)</th>
                    <th>Destino</th>
                    <th>Porteiro (Chegada)</th>
                @elseif ($reportType === 'official')
                    <th>Veículo (Placa)</th>
                    <th>Condutor</th>
                    <th>Partida</th>
                    <th>Chegada</th>
                    <th>Destino</th>
                    <th>Porteiro (Partida)</th>
                @else
                    {{-- private --}}
                    <th>Veículo (Placa)</th>
                    <th>Condutor</th>
                    <th>Entrada</th>
                    <th>Saída</th>
                    <th>Motivo</th>
                    <th>Porteiro (Saída)</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($results as $entry)
                <tr>
                    @if ($reportType === 'vehicle')
                        <td>{{ $entry->driver?->name ?? 'N/A' }}</td>
                        <td><span class="no-break">{{ $entry->entry_at?->format('d/m H:i') }}</span></td>
                        <td><span class="no-break">{{ $entry->exit_at?->format('d/m H:i') ?? '-' }}</span></td>
                        <td>{{ $entry->entry_reason }}</td> {{-- entry_reason foi mapeado do destination --}}
                        {{-- ### CORREÇÃO RELATÓRIO VEÍCULO ### --}}
                        <td>{{ $entry->guardExit?->name ?? 'N/A' }}</td> {{-- Mapeado de guardArrival --}}
                    @elseif ($reportType === 'official')
                        <td>
                            {{ $entry->vehicle?->model ?? 'N/A' }}<br>
                            <small
                                class="not-available no-break">{{ $entry->vehicle?->license_plate ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $entry->driver?->name ?? 'N/A' }}</td>
                        <td><span class="no-break">{{ $entry->departure_datetime?->format('d/m H:i') }}</span></td>
                        <td><span class="no-break">{{ $entry->arrival_datetime?->format('d/m H:i') ?? '-' }}</span>
                        </td>
                        <td>{{ $entry->destination }}</td>
                        {{-- ### CORREÇÃO RELATÓRIO PESSOAL OFICIAL ### --}}
                        <td>{{ $entry->guardDeparture?->name ?? 'N/A' }}</td> {{-- Mostra quem registrou a PARTIDA --}}
                    @else
                        {{-- private --}}
                        <td>
                            {{ $entry->vehicle_model ?? ($entry->vehicle?->model ?? 'N/A') }}<br>
                            <small
                                class="not-available no-break">{{ $entry->license_plate ?? ($entry->vehicle?->license_plate ?? 'N/A') }}</small>
                        </td>
                        <td>{{ $entry->driver?->name ?? 'N/A' }}</td>
                        <td><span class="no-break">{{ $entry->entry_at?->format('d/m H:i') }}</span></td>
                        <td><span class="no-break">{{ $entry->exit_at?->format('d/m H:i') ?? '-' }}</span></td>
                        <td>{{ $entry->entry_reason }}</td>
                        {{-- ### CORREÇÃO RELATÓRIO PESSOAL PARTICULAR ### --}}
                        <td>{{ $entry->guardExit?->name ?? 'N/A' }}</td> {{-- Mostra quem registrou a SAÍDA --}}
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ $colspan }}" class="text-center" style="padding: 15px;">
                        Nenhum registo encontrado para este período e filtros.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Seção de Assinatura --}}
    <table class="signature-section">
        <tr>
            @if ($reportType === 'vehicle')
                <td class="signature-line">
                    <strong>Responsável pelo Relatório</strong>
                </td>
            @else
                {{-- Relatório Pessoal --}}
                <td class="signature-line">
                    {{ $porteiroName }}<br>
                    <strong>Porteiro Responsável</strong>
                </td>
            @endif
        </tr>
    </table>

    {{-- Rodapé será adicionado pelo script PHP --}}

</body>

</html>
