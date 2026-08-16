<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Extrato de Frota Oficial</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            margin: 10px;
            color: #222;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #2c5282;
            /* Cor mais institucional */
            padding-bottom: 10px;
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

        .header-text h4 {
            font-size: 11px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            color: #1a202c;
        }

        .header-text h5 {
            font-size: 9px;
            margin: 3px 0 0 0;
            font-weight: normal;
            color: #4a5568;
        }

        .details-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 15px;
            width: 100%;
            box-sizing: border-box;
        }

        .details-box table {
            width: 100%;
            border: none;
        }

        .details-box td {
            font-size: 9px;
            border: none;
            padding: 2px 0;
            color: #2d3748;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background-color: #edf2f7;
            font-weight: bold;
            font-size: 8px;
            text-transform: uppercase;
            color: #4a5568;
        }

        tr:nth-child(even) {
            background-color: #fbfbfc;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e2e8f0;
            font-size: 10px;
            color: #1a202c;
            padding: 6px;
        }

        .no-break {
            white-space: nowrap;
        }

        .observation-row td {
            font-size: 8px;
            color: #4a5568;
            padding: 4px 6px;
            background-color: #fffaf0;
            border-top: 1px dashed #cbd5e1;
            text-align: left;
        }

        /* Assinatura */
        .signature-wrapper {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .signature-box {
            width: 250px;
            margin: 0 auto;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }

        .signature-text {
            font-size: 9px;
            color: #333;
        }

        /* Rodapé de Auditoria */
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            font-size: 7px;
            border-top: 1px solid #cbd5e1;
            padding-top: 4px;
            color: #718096;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>
    <script type="text/php">
        if (isset($pdf)) { 
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $size = 7;
            $font = $fontMetrics->getFont("Helvetica");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) - 20;
            $y = $pdf->get_height() - 20;
            $pdf->page_text($x, $y, $text, $font, $size, array(0.44, 0.50, 0.58));
        }
    </script>

    @include('reports.pdf._header', ['title' => 'EXTRATO DE AUDITORIA - FROTA OFICIAL'])

    <div class="details-box">
        <table>
            <tr>
                <td style="width: 33%;"><strong>Período Base:</strong> {{ $startDate }} a {{ $endDate }}</td>
                <td style="width: 33%;"><strong>Veículo Filtrado:</strong>
                    {{ $vehicle ? $vehicle->license_plate . ' - ' . $vehicle->model : 'Todos os veículos' }}</td>
                <td style="width: 34%;"><strong>Condutor Filtrado:</strong>
                    {{ $driver ? $driver->name : 'Todos os servidores autorizados' }}</td>
            </tr>
        </table>
    </div>

    <table class="main">
        <colgroup>
            <col style="width: 15%;">
            <col style="width: 15%;">
            <col style="width: 14%;">
            <col style="width: 14%;">
            <col style="width: 16%;">
            <col style="width: 9%;">
            <col style="width: 9%;">
            <col style="width: 8%;">
        </colgroup>
        <thead>
            <tr>
                <th>Viatura (Placa)</th>
                <th>Servidor / Condutor</th>
                <th>Partida <br>(Data/Hora - KM)</th>
                <th>Chegada <br>(Data/Hora - KM)</th>
                <th>Destino Registrado</th>
                <th>Porteiro<br>(Partida)</th>
                <th>Porteiro<br>(Chegada)</th>
                <th>Distância<br>Rodada</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trips as $trip)
                <tr>
                    <td>
                        <strong>{{ $trip->vehicle?->model ?? 'N/A' }}</strong><br>
                        <span
                            style="color: #4a5568; font-family: monospace;">{{ $trip->vehicle?->license_plate ?? 'N/A' }}</span>
                    </td>
                    <td>
                        {{ $trip->driver?->name ?? 'N/A' }}
                        @if ($trip->passengers)
                            <br><span style="color: #4a5568; font-size: 7px;">👤
                                {{ Str::limit($trip->passengers, 30) }}</span>
                        @endif
                    </td>
                    <td class="no-break">
                        {{ $trip->departure_datetime?->format('d/m/y H:i') }}<br>
                        <span style="color: #718096;">KM:
                            {{ number_format($trip->departure_odometer, 0, ',', '.') }}</span>
                    </td>
                    <td class="no-break">
                        {{ $trip->arrival_datetime?->format('d/m/y H:i') ?? 'Em viagem' }}<br>
                        <span style="color: #718096;">KM:
                            {{ $trip->arrival_odometer ? number_format($trip->arrival_odometer, 0, ',', '.') : '-' }}</span>
                    </td>
                    <td>{{ $trip->destination }}</td>
                    <td>{{ $trip->guardDeparture?->name ?? 'N/A' }}</td>
                    <td>{{ $trip->guardArrival?->name ?? 'N/A' }}</td>
                    <td style="font-weight: bold;">
                        {{ $trip->distance_traveled ? $trip->distance_traveled . ' km' : '-' }}</td>
                </tr>
                @if ($trip->return_observation)
                    <tr class="observation-row">
                        <td colspan="8">
                            <strong>Observação no Retorno:</strong> {{ $trip->return_observation }}
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 30px; color: #718096;">Nenhuma movimentação
                        oficial encontrada para os filtros selecionados.</td>
                </tr>
            @endforelse
        </tbody>
        @if ($trips->isNotEmpty())
            <tfoot class="total-row">
                <tr>
                    <td colspan="7" style="text-align: right; border-right: none;">Total de viagens listadas:
                        {{ $trips->count() }} | <strong>Quilometragem Total do Período:</strong></td>
                    <td style="text-align: center; border-left: none;">{{ number_format($totalKm, 0, ',', '.') }} km
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    <div class="signature-wrapper">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-text">
                <strong>{{ $generatorName ?? 'Sistema' }}</strong><br>
                Responsável pela Geração do Documento<br>
                <span style="color: #718096; font-size: 7px;">Perfil:
                    {{ Str::upper($generatorRole ?? 'Usuário') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <table style="width: 100%; border: none; font-size: 7px; color: #718096;">
            <tr>
                <td style="text-align: left; border: none;">SIGA-IF | Sistema de Gestão de Frotas e Portaria</td>
                <td style="text-align: center; border: none;">Documento de Auditoria - Gerado em
                    {{ now()->format('d/m/Y \à\s H:i:s') }}</td>
                <td style="text-align: right; border: none;"></td>
            </tr>
        </table>
    </div>
</body>

</html>
