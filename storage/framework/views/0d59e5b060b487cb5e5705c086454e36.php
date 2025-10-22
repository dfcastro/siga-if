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
            font-size: 12px;
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
            text-align: center;
            font-size: 10px;
            vertical-align: middle;
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
    
    <script type="text/php">
        if (isset($pdf)) { /* ... (script mantido) ... */ }
    </script>

    
    <?php echo $__env->make('reports.pdf._header', ['title' => 'RELATÓRIO DE UTILIZAÇÃO DA FROTA OFICIAL'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <table class="details">
        <tr>
            <td><strong>Período:</strong> <?php echo e($startDate); ?> a <?php echo e($endDate); ?></td>
            <?php if($vehicle): ?>
                <td><strong>Veículo:</strong> <?php echo e($vehicle->model); ?> (<?php echo e($vehicle->license_plate); ?>)</td>
            <?php endif; ?>
            <?php if($driver): ?>
                <td><strong>Motorista:</strong> <?php echo e($driver->name); ?></td>
            <?php endif; ?>
        </tr>
    </table>

    
    <table class="main">
        <colgroup>
            
            <col style="width: 15%;"> 
            <col style="width: 15%;"> 
            <col style="width: 15%;"> 
            <col style="width: 15%;"> 
            <col style="width: 14%;"> 
            <col style="width: 9%;"> 
            <col style="width: 9%;"> 
            <col style="width: 8%;"> 
        </colgroup>
        <thead>
            <tr>
                
                <th>Veículo (Placa)</th>
                <th>Condutor</th>
                <th>Partida (Data/Hora - KM)</th>
                <th>Chegada (Data/Hora - KM)</th>
                <th>Destino</th>
                <th>Porteiro (Partida)</th>
                <th>Porteiro (Chegada)</th>
                <th style="text-align: center;">KM Rodado</th> 
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    
                    <td><?php echo e($trip->vehicle?->model ?? 'N/A'); ?><br><small style="color: #555;"
                            class="no-break">(<?php echo e($trip->vehicle?->license_plate ?? 'N/A'); ?>)</small></td>

                    
                    <td>
                        <?php echo e($trip->driver?->name ?? 'N/A'); ?>

                        <?php if($trip->passengers): ?>
                            <br><small style="color: #444; font-size: 7px;">
                                <strong>Passag.:</strong> <?php echo e($trip->passengers); ?>

                            </small>
                        <?php endif; ?>
                    </td>

                    
                    <td class="no-break">
                        <?php echo e($trip->departure_datetime?->format('d/m H:i')); ?><br><small><?php echo e(number_format($trip->departure_odometer, 0, ',', '.')); ?>

                            km</small></td>

                    
                    <td class="no-break">
                        <?php echo e($trip->arrival_datetime?->format('d/m H:i') ?? '-'); ?><br><small><?php echo e($trip->arrival_odometer ? number_format($trip->arrival_odometer, 0, ',', '.') . ' km' : '-'); ?></small>
                    </td>

                    
                    <td><?php echo e($trip->destination); ?></td>

                    
                    <td><?php echo e($trip->guardDeparture?->name ?? 'N/A'); ?></td>
                    <td><?php echo e($trip->guardArrival?->name ?? 'N/A'); ?></td>

                    
                    <td style="text-align: center;"><?php echo e($trip->distance_traveled ?? 'N/A'); ?></td>
                </tr>

                
                <?php if($trip->return_observation): ?>
                    <tr class="observation-row">
                        <td colspan="8"> 
                            <strong>Obs. Retorno:</strong> <?php echo e($trip->return_observation); ?>

                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Nenhuma viagem encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if($trips->isNotEmpty()): ?>
            <tfoot class="total-row">
                <tr>
                    

                    
                    <td colspan="7" style="text-align: right; border-right: none;"><strong>Distância Total Rodada no
                            Período:</strong></td>

                    
                    <td style="text-align: center; border-left: none;">
                        <strong><?php echo e(number_format($totalKm, 0, ',', '.')); ?> km</strong>
                    </td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>

    
    <div
        style="margin-top: 50px; /* Espaçamento */
                page-break-inside: avoid;
                width: 280px; /* Largura ajustada */
                margin-left: auto;
                margin-right: auto;
                text-align: center;">

        
        <div style="border-top: 1px solid #333; height: 1px; margin-bottom: 5px;">
            
        </div>

        
        <div style="font-size: 9px;">
            
            <?php echo e($generatorName ?? 'Usuário Desconhecido'); ?><br>
            <strong>Responsável pela Emissão</strong> 
        </div>
    </div>
    


    
    <div class="footer">
        <span class="generation-date">Gerado em: <?php echo e(now()->format('d/m/Y H:i')); ?></span>
        <span class="page-number"></span>
    </div>

</body>

</html>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/reports/pdf/official.blade.php ENDPATH**/ ?>