<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Relatório de Veículos Particulares</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            margin: 25px;
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
            width: 120px;
        }

        .logo-siga {
            width: 100px;
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
            font-size: 11px;
            font-weight: bold;
        }

        .details {
            margin-bottom: 15px;
            border-collapse: collapse;
            width: 100%;
        }

        .details td {
            border: 1px solid #ddd;
            padding: 6px;
            font-size: 10px;
            background-color: #f9f9f9;
        }

        table.main {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 5px;
            text-align: left;
            vertical-align: middle;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 8px;
            text-align: right;
        }

        .page-number:before {
            content: "Página " counter(page);
        }
    </style>
</head>

<body>
    <?php echo $__env->make('reports.pdf._header', ['title' => 'RELATÓRIO DE ENTRADA E SAÍDA DE VEÍCULOS PARTICULARES'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table class="details">
        <tr>
            <td><strong>Período:</strong> <?php echo e(\Carbon\Carbon::parse($startDate)->format('d/m/Y')); ?> a
                <?php echo e(\Carbon\Carbon::parse($endDate)->format('d/m/Y')); ?></td>
            <?php if($vehicle): ?>
                <td><strong>Veículo:</strong> <?php echo e($vehicle->model); ?> (<?php echo e($vehicle->license_plate); ?>)</td>
            <?php endif; ?>
            <?php if($driver): ?>
                <td><strong>Motorista:</strong> <?php echo e($driver->name); ?></td>
            <?php endif; ?>
        </tr>
    </table>

    <table class="main">
        <thead>
            <tr>
                <th style="width: 25%;">Veículo</th>
                <th style="width: 25%;">Condutor</th>
                <th style="width: 20%;">Entrada / Saída</th>
                <th style="width: 20%;">Motivo</th>
                <th style="width: 10%;">Porteiro</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($entry->vehicle?->model ?? $entry->vehicle_model); ?><br><small
                            style="color: #555;"><?php echo e($entry->vehicle?->license_plate ?? $entry->license_plate); ?></small>
                    </td>
                    <td><?php echo e($entry->driver?->name ?? 'Não informado'); ?></td>
                    <td><?php echo e($entry->entry_at->format('d/m/Y H:i')); ?><br><?php echo e($entry->exit_at ? $entry->exit_at->format('d/m/Y H:i') : ''); ?>

                    </td>
                    <td><?php echo e($entry->entry_reason); ?></td>
                    <td><?php echo e($entry->guard_on_entry); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Nenhuma entrada encontrada.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="footer"><span class="page-number"></span></div>
</body>

</html>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/reports/pdf/private.blade.php ENDPATH**/ ?>