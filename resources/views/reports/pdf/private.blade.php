<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Relatório de Veículos Particulares</title>
    {{-- ### ESTILOS AJUSTADOS PARA SIMILARIDADE COM official.blade.php ### --}}
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; margin: 25px; color: #333; } /* Tamanho fonte base 8px */
        .header-table { width: 100%; border-bottom: 1px solid #333; padding-bottom: 8px; margin-bottom: 15px; }
        .header-table td { vertical-align: middle; border: none; }
        .logo-ifnmg { width: 100px; } /* Ajustado */
        .logo-siga { width: 80px; } /* Ajustado */
        .header-text { text-align: center; }
        .header-text h4, .header-text h5 { margin: 0; font-weight: normal; }
        .header-text h4 { font-size: 10px; font-weight: bold; } /* Ajustado */
        .header-text h5 { font-size: 12px; } /* Ajustado */
        .details { margin-bottom: 15px; border-collapse: collapse; width: 100%; }
        .details td { border: 1px solid #ddd; padding: 5px; font-size: 9px; background-color: #f9f9f9; } /* Ajustado */
        table.main { width: 100%; border-collapse: collapse; table-layout: fixed; margin-bottom: 40px; /* Espaço assinatura */ }
        th, td { border: 1px solid #ccc; padding: 4px; text-align: center; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f2f2f2; font-weight: bold; font-size: 10px; text-transform: uppercase; white-space: nowrap; } /* Estilo TH igual */
        .footer { position: fixed; bottom: -20px; left: 0; right: 0; height: 40px; font-size: 8px; border-top: 0.5px solid #ccc; padding-top: 5px; margin: 0 25px; }
        .footer .page-number { float: right; }
        .footer .generation-date { float: left; }
        tr { page-break-inside: avoid !important; }
        thead { display: table-header-group; }
        /* tfoot não é necessário aqui, mas mantemos a regra */
        tfoot { display: table-row-group; }
        .no-break { white-space: nowrap; }
        /* Estilos para Assinatura (iguais ao official.blade.php) */
        .signature-section { width: 250px; margin: 60px auto 0 auto; text-align: center; page-break-inside: avoid; }
        .signature-line { border-top: 1px solid #333; padding-top: 5px; font-size: 9px; }
    </style>
</head>
<body>
    {{-- Script PHP para rodapé --}}
    <script type="text/php">
        if (isset($pdf)) { /* ... (script mantido) ... */ }
    </script>

    {{-- Cabeçalho --}}
    @include('reports.pdf._header', ['title' => 'RELATÓRIO DE ENTRADA E SAÍDA DE VEÍCULOS PARTICULARES'])

    {{-- Detalhes do Filtro --}}
    <table class="details">
        <tr>
            <td><strong>Período:</strong> {{ $startDate }} a {{ $endDate }}</td>
            @if ($vehicle) <td><strong>Veículo Filtrado:</strong> {{ $vehicle->model }} ({{ $vehicle->license_plate }})</td> @endif
            @if ($driver) <td><strong>Motorista Filtrado:</strong> {{ $driver->name }}</td> @endif
        </tr>
    </table>

    {{-- Tabela Principal --}}
    <table class="main">
        {{-- ### COGROUP AJUSTADO PARA CONSISTÊNCIA ### --}}
        <colgroup>
            <col style="width: 20%;"> {{-- Veículo --}}
            <col style="width: 20%;"> {{-- Condutor --}}
            <col style="width: 15%;"> {{-- Entrada --}}
            <col style="width: 15%;"> {{-- Saída --}}
            <col style="width: 20%;"> {{-- Motivo --}}
            <col style="width: 10%;"> {{-- Porteiro (Saída) --}}
        </colgroup>
        <thead>
            <tr>
                <th>Veículo (Placa)</th>
                <th>Condutor</th>
                <th>Entrada (Data/Hora)</th> {{-- Título ajustado --}}
                <th>Saída (Data/Hora)</th>   {{-- Título ajustado --}}
                <th>Motivo</th>
                <th>Porteiro (Saída)</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($entries as $entry)
                <tr>
                    <td>
                        {{ $entry->vehicle_model ?? ($entry->vehicle?->model ?? 'N/A') }}
                        <br><small style="color: #555;" class="no-break">({{ $entry->license_plate ?? ($entry->vehicle?->license_plate ?? 'N/A') }})</small>
                    </td>
                    <td>{{ $entry->driver?->name ?? 'Não informado' }}</td>
                    <td class="no-break">{{ $entry->entry_at?->format('d/m H:i') }}</td>
                    <td class="no-break">{{ $entry->exit_at?->format('d/m H:i') ?? '-' }}</td>
                    <td>{{ $entry->entry_reason }}</td>
                    {{-- Usa a relação guardExit --}}
                    <td>{{ $entry->guardExit?->name ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; padding: 20px;">Nenhuma entrada encontrada.</td></tr>
            @endforelse
        </tbody>
        {{-- Não há tfoot com totais aqui --}}
    </table>

    {{-- ### INÍCIO - SEÇÃO DE ASSINATURA (igual ao official.blade.php) ### --}}
    <table class="signature-section">
        <tr>
            <td style="border: none;" class="signature-line">
                 @if(isset($porteiroName))
                     {{ $porteiroName }}<br>
                     <strong>Porteiro Responsável</strong>
                @else
                     <br>
                    <strong>Responsável pelo Relatório</strong>
                @endif
            </td>
        </tr>
    </table>
    {{-- ### FIM - SEÇÃO DE ASSINATURA ### --}}

    {{-- Rodapé da Página --}}
    <div class="footer">
        <span class="generation-date">Gerado em: {{ now()->format('d/m/Y H:i') }}</span>
        <span class="page-number"></span>
    </div>
</body>
</html>