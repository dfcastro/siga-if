<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Controle de Veículo Oficial</title>
    <style>
        /* Estilos gerais */
        body { font-family: 'Helvetica', sans-serif; font-size: 9px; margin: 25px; }
        
        /* Cabeçalho */
        .header-table { width: 100%; border: none; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; }
        .logo-ifnmg { width: 60px; }
        .logo-siga { width: 100px; }
        .header-text { text-align: center; }
        .header-text h4, .header-text h5 { margin: 0; font-weight: normal; }
        .header-text h4 { font-size: 11px; font-weight: bold; }

        /* Detalhes do veículo */
        .details { margin-bottom: 10px; border: 1px solid #000; padding: 5px; }
        .details span { margin-right: 25px; font-size: 10px; }
        
        /* Tabela principal */
        table.main { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 3px; text-align: center; word-wrap: break-word; }
        th { background-color: #e3e3e3; font-weight: bold; }
        
        /* Larguras específicas para as colunas */
        .col-dia { width: 4%; }
        .col-hora { width: 6%; }
        .col-km { width: 8%; }
        .col-destino { width: 20%; text-align: left; padding-left: 4px; }
        .col-passageiros { width: 20%; text-align: left; padding-left: 4px; }
        .col-condutor { width: 16%; }
        .col-porteiro { width: 12%; }
        
        /* Rodapé */
        .footer { margin-top: 30px; }
        .signature { float: right; }
    </style>
</head>
<body>
    {{-- CABEÇALHO COM LOGOS --}}
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left;">
                <img src="{{ public_path('images/logo-ifnmg-navigation.png') }}" alt="Logo IFNMG" class="logo-ifnmg">
            </td>
            <td class="header-text" style="width: 70%;">
                <h4>MINISTÉRIO DA EDUCAÇÃO</h4>
                <h5>SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</h5>
                <h5>INSTITUTO FEDERAL DO NORTE DE MINAS GERAIS – CAMPUS ALMENARA</h5>
                <hr>
                <h4>CONTROLE DE SAÍDA E CHEGADA DE VEÍCULOS OFICIAIS</h4>
            </td>
            <td style="width: 15%; text-align: right;">
                <img src="{{ public_path('images/logo-siga.png') }}" alt="Logo SIGA" class="logo-siga">
            </td>
        </tr>
    </table>

    <div class="details">
        <span><strong>Automóvel:</strong> {{ $vehicles->model }}</span>
        <span><strong>Placa:</strong> {{ $vehicles->license_plate }}</span>
        <span><strong>Período (mês/ano):</strong> {{ \Carbon\Carbon::parse($startDate)->format('m/Y') }}</span>
    </div>

    <table class="main">
        <thead>
            <tr>
                <th colspan="3">SAÍDA</th>
                <th colspan="3">CHEGADA</th>
                <th rowspan="2" class="col-destino">DESTINO</th>
                <th rowspan="2" class="col-passageiros">PASSAGEIROS</th>
                <th rowspan="2" class="col-condutor">CONDUTOR</th>
                <th rowspan="2" class="col-porteiro">PORTEIRO (Saída)</th>
            </tr>
            <tr>
                <th class="col-dia">DIA</th>
                <th class="col-hora">HORA</th>
                <th class="col-km">KM</th>
                <th class="col-dia">DIA</th>
                <th class="col-hora">HORA</th>
                <th class="col-km">KM</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($trips as $trip)
                <tr>
                    <td>{{ $trip->departure_datetime->format('d') }}</td>
                    <td>{{ $trip->departure_datetime->format('H:i') }}</td>
                    <td>{{ number_format($trip->departure_odometer, 0, ',', '.') }}</td>
                    <td>{{ $trip->arrival_datetime?->format('d') }}</td>
                    <td>{{ $trip->arrival_datetime?->format('H:i') }}</td>
                    <td>{{ $trip->arrival_odometer ? number_format($trip->arrival_odometer, 0, ',', '.') : '' }}</td>
                    <td class="col-destino">{{ $trip->destination }}</td>
                    <td class="col-passageiros">{{ $trip->passengers ?: 'N/A' }}</td>
                    <td>{{ $trip->driver->name }}</td>
                    <td>{{ $trip->guard_on_departure }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="padding: 15px;">Nenhuma viagem encontrada para este veículo no período selecionado.</td>
                </tr>
            @endforelse
            {{-- Adiciona linhas em branco para preencher a folha --}}
            @for ($i = 0; $i < (15 - $trips->count()); $i++)
                <tr>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                    <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                </tr>
            @endfor
        </tbody>
    </table>

    <div class="footer">
        <span><strong>KM(s) Percorridos/mês:</strong> {{ number_format($totalKm, 0, ',', '.') }}</span>
        <span class="signature"><strong>Assinatura responsável:</strong> _________________________</span>
    </div>
</body>
</html>