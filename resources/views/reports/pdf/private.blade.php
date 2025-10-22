<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Relatório de Veículos Particulares</title>
    {{-- ### ESTILOS AJUSTADOS PARA SIMILARIDADE ### --}}
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            margin: 25px;
            color: #333;
        }

        .header-table {
            width: 100%;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }

        .header-table td {
            vertical-align: middle;
            border: none;
        }

        .logo-ifnmg {
            width: 100px;
        }

        .logo-siga {
            width: 80px;
        }

        .header-text {
            text-align: center;
        }

        .header-text h4,
        .header-text h5 {
            margin: 0;
            font-weight: normal;
        }

        .header-text h4 {
            font-size: 10px;
            font-weight: bold;
        }

        .header-text h5 {
            font-size: 8px;
        }

        hr {
            border: none;
            border-top: 0.5px solid #333;
            margin: 2px 0;
        }

        .details {
            margin-bottom: 15px;
            border-collapse: collapse;
            width: 100%;
        }

        .details td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 12px;
            background-color: #f9f9f9;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 40px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px;
            text-align: center;
            font-size: 10px;
            vertical-align: middle;;
            word-wrap: break-word;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .footer {
            position: fixed;
            bottom: -20px;
            left: 0;
            right: 0;
            height: 40px;
            font-size: 8px;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
            margin: 0 25px;
        }

        .footer .page-number {
            float: right;
        }

        .footer .generation-date {
            float: left;
        }

        tr {
            page-break-inside: avoid !important;
        }

        td {
            page-break-inside: avoid !important;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        .no-break {
            white-space: nowrap;
        }

        .signature-section {
            margin-top: 50px;
            /* Ou o espaço que preferir */
            page-break-inside: avoid;
            /* Manter a tentativa */
            text-align: center;
            width: 280px;
            /* Largura ajustada */
            margin-left: auto;
            margin-right: auto;
        }

        .signature-line {
            border-top: 1px solid #333;
            height: 1px;
            margin-bottom: 5px;
        }

        .signature-text {
            font-size: 9px;
            text-align: center;
            /* Garantir centralização do texto */
        }
    </style>
</head>

<body>
    {{-- Script PHP para rodapé --}}
    <script type="text/php"> /* ... (script mantido) ... */ </script>

    {{-- Cabeçalho --}}
    @include('reports.pdf._header', ['title' => 'RELATÓRIO DE ENTRADA E SAÍDA DE VEÍCULOS PARTICULARES'])

    {{-- Detalhes do Filtro --}}
    <table class="details">
        <tr>
            <td><strong>Período:</strong> {{ $startDate }} a {{ $endDate }}</td>
            @if ($vehicle)
                <td><strong>Veículo Filtrado:</strong> {{ $vehicle->model }} ({{ $vehicle->license_plate }})</td>
            @endif
            @if ($driver)
                <td><strong>Motorista Filtrado:</strong> {{ $driver->name }}</td>
            @endif
        </tr>
    </table>

    {{-- Tabela Principal --}}
    <table class="main">
        {{-- Colgroup ajustado --}}
        <colgroup>
            <col style="width: 20%;">
            <col style="width: 20%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 20%;">
            <col style="width: 10%;">
        </colgroup>
        <thead>
            <tr>
                <th>Veículo (Placa)</th>
                <th>Condutor</th>
                <th>Entrada (Data/Hora)</th>
                <th>Saída (Data/Hora)</th>
                <th>Motivo</th>
                <th>Porteiro (Saída)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>{{ $entry->vehicle_model ?? ($entry->vehicle?->model ?? 'N/A') }}<br><small style="color: #555;"
                            class="no-break">({{ $entry->license_plate ?? ($entry->vehicle?->license_plate ?? 'N/A') }})</small>
                    </td>
                    <td>{{ $entry->driver?->name ?? 'Não informado' }}</td>
                    <td class="no-break">{{ $entry->entry_at?->format('d/m H:i') }}</td>
                    <td class="no-break">{{ $entry->exit_at?->format('d/m H:i') ?? '-' }}</td>
                    <td>{{ $entry->entry_reason }}</td>
                    <td>{{ $entry->guardExit?->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma entrada encontrada.</td>
                </tr>
            @endforelse
        </tbody>
        {{-- Não há tfoot aqui --}}
    </table>

    {{-- ### INÍCIO - SEÇÃO DE ASSINATURA CORRIGIDA (Alinhamento) ### --}}
    <div
        style="margin-top: 50px;
                page-break-inside: avoid;
                width: 280px;
                margin-left: auto;
                margin-right: auto;
                text-align: center;">
        {{-- Centraliza a linha --}}

        {{-- ### INÍCIO - SEÇÃO DE ASSINATURA COM CLASSES CSS ### --}}
        <div class="signature-section">
            {{-- Linha --}}
            <div class="signature-line"></div>

            {{-- Texto --}}
            <div class="signature-text">
                @if (isset($porteiroName))
                    {{ $porteiroName ?? Auth::user()->name }}<br>
                    <strong>Porteiro Responsável</strong>
                @else
                    {{ $generatorName ?? 'Usuário Desconhecido' }}<br> {{-- Usa a variável passada pelo Controller --}}
                    <strong>Responsável pela Emissão</strong>
                @endif
            </div>
        </div>
        {{-- ### FIM - SEÇÃO DE ASSINATURA COM CLASSES CSS ### --}}

        {{-- Rodapé da Página --}}
        <div class="footer">
            <span class="generation-date">Gerado em: {{ now()->format('d/m/Y H:i') }}</span>
            <span class="page-number"></span>
        </div>
</body>

</html>
