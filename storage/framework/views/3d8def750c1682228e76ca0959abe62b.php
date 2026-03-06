<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Extrato de Entradas Particulares</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 8px;
            margin: 10px;
            color: #222;
        }

        .header-table {
            width: 100%;
            border-bottom: 2px solid #38a169;
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
            background-color: #f0fff4;
            border: 1px solid #c6f6d5;
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
            color: #276749;
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

    <?php echo $__env->make('reports.pdf._header', ['title' => 'EXTRATO DE AUDITORIA - VEÍCULOS PARTICULARES'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="details-box">
        <table>
            <tr>
                <td style="width: 33%;"><strong>Período Base:</strong> <?php echo e($startDate); ?> a <?php echo e($endDate); ?></td>
                <td style="width: 33%;"><strong>Veículo Filtrado:</strong>
                    <?php echo e($vehicle ? $vehicle->license_plate . ' - ' . $vehicle->model : 'Todos os veículos'); ?></td>
                <td style="width: 34%;"><strong>Condutor Filtrado:</strong>
                    <?php echo e($driver ? $driver->name : 'Todos os condutores/visitantes'); ?></td>
            </tr>
        </table>
    </div>

    <table class="main">
        <colgroup>
            <col style="width: 15%;"> 
            <col style="width: 16%;"> 
            <col style="width: 12%;"> 
            <col style="width: 12%;"> 
            <col style="width: 23%;"> 
            <col style="width: 11%;"> 
            <col style="width: 11%;"> 
        </colgroup>
        <thead>
            <tr>
                <th>Veículo (Placa)</th>
                <th>Condutor Responsável</th>
                <th>Acesso Liberado <br>(Data/Hora)</th>
                <th>Saída Registrada <br>(Data/Hora)</th>
                <th>Motivo / Destino Declarado</th>
                <th>Porteiro<br>(Entrada)</th>
                <th>Porteiro<br>(Saída)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td>
                        <strong><?php echo e($entry->vehicle_model ?? ($entry->vehicle?->model ?? 'N/A')); ?></strong><br>
                        <span
                            style="color: #4a5568; font-family: monospace;"><?php echo e($entry->license_plate ?? ($entry->vehicle?->license_plate ?? 'N/A')); ?></span>
                    </td>
                    <td><?php echo e($entry->driver?->name ?? 'Não informado'); ?></td>
                    <td class="no-break"><?php echo e($entry->entry_at?->format('d/m/y H:i')); ?></td>
                    <td class="no-break"><?php echo e($entry->exit_at?->format('d/m/y H:i') ?? 'No campus'); ?></td>
                    <td><?php echo e($entry->entry_reason); ?></td>
                    <td><?php echo e($entry->guardEntry?->name ?? 'N/A'); ?></td>
                    <td><?php echo e($entry->guardExit?->name ?? 'N/A'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 30px; color: #718096;">Nenhuma entrada de
                        particulares encontrada para os filtros selecionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <?php if($entries->isNotEmpty()): ?>
            <tfoot class="total-row">
                <tr>
                    <td colspan="6" style="text-align: right; border-right: none;"><strong>Total de acessos
                            registrados no período:</strong></td>
                    <td style="text-align: center; border-left: none;"><?php echo e($entries->count()); ?></td>
                </tr>
            </tfoot>
        <?php endif; ?>
    </table>

    <div class="signature-wrapper">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-text">
                <strong><?php echo e($generatorName ?? 'Sistema'); ?></strong><br>
                Responsável pela Geração do Documento<br>
                <span style="color: #718096; font-size: 7px;">Perfil:
                    <?php echo e(Str::upper($generatorRole ?? 'Usuário')); ?></span>
            </div>
        </div>
    </div>

    <div class="footer">
        <table style="width: 100%; border: none; font-size: 7px; color: #718096;">
            <tr>
                <td style="text-align: left; border: none;">SIGA-IF | Sistema de Gestão de Frotas e Portaria</td>
                <td style="text-align: center; border: none;">Documento de Auditoria - Gerado em
                    <?php echo e(now()->format('d/m/Y \à\s H:i:s')); ?></td>
                <td style="text-align: right; border: none;"></td>
            </tr>
        </table>
    </div>
</body>

</html>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/reports/pdf/private.blade.php ENDPATH**/ ?>