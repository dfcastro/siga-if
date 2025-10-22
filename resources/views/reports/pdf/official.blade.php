<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Relatório de Frota Oficial</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            margin: 10px;
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

        .details {
            margin-bottom: 15px;
            border-collapse: collapse;
            width: 100%;
        }

        .details td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 9px;
            background-color: #f9f9f9;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 40px;
            /* Espaço antes da assinatura */
            /* ### NOVO: Tentar forçar a não quebra antes/depois da tabela inteira ### */
            page-break-before: auto;
            page-break-after: auto;
            page-break-inside: auto;
            /* Pode remover se causar problemas */
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 4px;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            /* ### NOVO: Tentar forçar a não quebra DENTRO das células ### */
            page-break-inside: avoid !important;
        }

        th {
            /* Estilos do TH mantidos */
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 7px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        tr {
            /* ### MODIFICADO: Mantém o avoid, mas remove !important por agora ### */
            /* page-break-inside: avoid !important; */
            page-break-inside: avoid;
            /* ### NOVO: Tenta explicitamente impedir quebras antes/depois de CADA linha ### */
            page-break-before: auto;
            page-break-after: auto;
        }

        thead {
            display: table-header-group;
            /* ### NOVO: Tentar forçar a não quebra antes/depois do cabeçalho ### */
            page-break-before: auto;
            page-break-after: auto;
        }

        tbody {
            /* ### NOVO: Tentar forçar a não quebra antes/depois do corpo ### */
            page-break-before: auto;
            page-break-after: auto;
        }

        tfoot {
            display: table-row-group;
            /* ### NOVO: Tentar forçar a não quebra antes/depois do rodapé da tabela ### */
            page-break-before: auto;
            /* Pode ajudar a manter o tfoot junto */
            page-break-after: auto;
        }

        .total-row td {
            font-weight: bold;
            background-color: #f8f8f8;
        }

        .no-break {
            white-space: nowrap;
        }

        .observation-row td {
            font-size: 7px;
            color: #555;
            padding: 2px 4px;
            border-top: none;
            background-color: #fdfdfd;
        }

        /* Estilos para Assinatura */
        .signature-section {
            width: 250px;
            margin: 40px auto 0 auto;
            /* Margem superior reduzida */
            text-align: center;
            /* page-break-inside: avoid; */
            /* REMOVIDO */
        }

        .signature-line {
            border-top: 1px solid #333;
            padding-top: 5px;
            font-size: 9px;
        }
    </style>
</head>

<body>
    {{-- Script PHP para rodapé --}}
    <script type="text/php">
        if (isset($pdf)) { /* ... (script mantido) ... */ }
    </script>

    {{-- Cabeçalho --}}
    @include('reports.pdf._header', ['title' => 'RELATÓRIO DE UTILIZAÇÃO DA FROTA OFICIAL'])

    {{-- Detalhes do Filtro --}}
    <table class="details">
        <tr>
            <td><strong>Período:</strong> {{ $startDate }} a {{ $endDate }}</td>
            @if ($vehicle)
                <td><strong>Veículo:</strong> {{ $vehicle->model }} ({{ $vehicle->license_plate }})</td>
            @endif
            @if ($driver)
                <td><strong>Motorista:</strong> {{ $driver->name }}</td>
            @endif
        </tr>
    </table>

    {{-- Tabela Principal --}}
    <table class="main">
        <colgroup>
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 14%;">
            <col style="width: 8%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
        </colgroup>
        <thead>
            <tr>
                <th>Veículo (Placa)</th>
                <th>Condutor</th>
                <th>Partida (Data/Hora - KM)</th>
                <th>Chegada (Data/Hora - KM)</th>
                <th>Destino</th>
                <th>KM Rodado</th>
                <th>Porteiro (Partida)</th>
                <th>Porteiro (Chegada)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trips as $trip)
                <tr>
                    <td>{{ $trip->vehicle?->model ?? 'N/A' }}<br><small style="color: #555;"
                            class="no-break">({{ $trip->vehicle?->license_plate ?? 'N/A' }})</small></td>
                    <td>{{ $trip->driver?->name ?? 'N/A' }}</td>
                    <td class="no-break">
                        {{ $trip->departure_datetime?->format('d/m H:i') }}<br><small>{{ number_format($trip->departure_odometer, 0, ',', '.') }}
                            km</small></td>
                    <td class="no-break">
                        {{ $trip->arrival_datetime?->format('d/m H:i') ?? '-' }}<br><small>{{ $trip->arrival_odometer ? number_format($trip->arrival_odometer, 0, ',', '.') . ' km' : '-' }}</small>
                    </td>
                    <td>{{ $trip->destination }}</td>
                    <td style="text-align: right;">{{ $trip->distance_traveled ?? 'N/A' }}</td>
                    <td>{{ $trip->guardDeparture?->name ?? 'N/A' }}</td>
                    <td>{{ $trip->guardArrival?->name ?? 'N/A' }}</td>
                </tr>
                @if ($trip->return_observation || $trip->passengers)
                    <tr class="observation-row">
                        <td colspan="8">
                            @if ($trip->passengers)
                                <strong>Passageiros:</strong> {{ $trip->passengers }}<br>
                                @endif @if ($trip->return_observation)
                                    <strong>Obs. Retorno:</strong> {{ $trip->return_observation }}
                                @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Nenhuma viagem encontrada.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($trips->isNotEmpty())
            <tfoot class="total-row">
                <tr>
                    <td colspan="5" style="text-align: right;"><strong>Distância Total Rodada no Período:</strong>
                    </td>
                    <td style="text-align: right;"><strong>{{ number_format($totalKm, 0, ',', '.') }} km</strong></td>
                    <td></td>
                    <td></td> {{-- Células vazias corrigidas --}}
                </tr>
            </tfoot>
        @endif
    </table>

    {{-- ### INÍCIO - NOVA SEÇÃO DE ASSINATURA COM DIV ### --}}
    <div
        style="margin-top: 50px; /* Ajuste o espaço conforme necessário */
                page-break-inside: avoid; /* Tenta manter a div numa página */
                text-align: center;
                width: 250px;
                margin-left: auto;
                margin-right: auto;">

        <div
            style="border-top: 1px solid #333;
                    padding-top: 5px;
                    font-size: 9px;">
            @if (isset($porteiroName))
                {{ $porteiroName }}<br>
                <strong>Porteiro Responsável</strong>
            @else
                {{-- Linha em branco para espaço ou texto genérico --}}
                <br>
                <strong>Responsável pelo Relatório</strong>
            @endif
        </div>
    </div>
    {{-- ### FIM - NOVA SEÇÃO DE ASSINATURA COM DIV ### --}}


    {{-- Rodapé da Página --}}
    <div class="footer">
        <span class="generation-date">Gerado em: {{ now()->format('d/m/Y H:i') }}</span>
        <span class="page-number"></span>
    </div>

</body>

</html>
