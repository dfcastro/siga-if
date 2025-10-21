<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Controle de Veículo Oficial</title>
    <style>
        /* Estilos gerais */
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 9px;
            margin: 25px;
        }

        /* Cabeçalho */
        .header-table {
            width: 100%;
            border: none;
            margin-bottom: 15px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .header-table td {
            vertical-align: middle;
            border: none;
        }

        .logo-ifnmg {
            width: 130px;
        }

        .logo-siga {
            width: 130px;
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
            font-size: 12px;
            font-weight: bold;
        }

        .header-text h5 {
            font-size: 10px;
        }

        /* Detalhes do veículo */
        .details {
            margin-bottom: 10px;
            border: 1px solid #000;
            padding: 5px;
        }

        .details span {
            margin-right: 30px;
            font-size: 10px;
        }

        /* Tabela principal */
        table.main {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #e3e3e3;
            font-weight: bold;
        }

        /* Larguras otimizadas para paisagem */
        .col-dia {
            width: 4%;
        }

        .col-hora {
            width: 5%;
        }

        .col-km {
            width: 7%;
        }

        .col-destino {
            width: 22%;
            text-align: left;
            padding-left: 5px;
        }

        .col-passageiros {
            width: 22%;
            text-align: left;
            padding-left: 5px;
        }

        .col-condutor {
            width: 15%;
        }

        .col-porteiro {
            width: 10%;
        }

        /* Rodapé */
        .footer {
            margin-top: 25px;
        }

        .signature {
            float: right;
            line-height: 40px;
        }

        /* Alinha a assinatura com a caixa de KM */

        /* NOVO: Caixa de destaque para os KMs */
        .km-box {
            float: left;
            border: 1px solid #000;
            padding: 8px;
            background-color: #f2f2f2;
        }

        .km-box span {
            font-size: 12px;
            /* Fonte maior */
            font-weight: bold;
            /* Negrito */
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 20%; text-align: left;">
                <img src="<?php echo e(public_path('images/logo-ifnmg.png')); ?>" alt="Logo IFNMG" class="logo-ifnmg">
            </td>
            <td class="header-text" style="width: 60%;">
                <h4>MINISTÉRIO DA EDUCAÇÃO</h4>
                <h5>SECRETARIA DE EDUCAÇÃO PROFISSIONAL E TECNOLÓGICA</h5>
                <h5>INSTITUTO FEDERAL DO NORTE DE MINAS GERAIS – CAMPUS ALMENARA</h5>
                <h4>CONTROLE DE SAÍDA E CHEGADA DE VEÍCULOS OFICIAIS</h4>
            </td>
            <td style="width: 20%; text-align: right;">
                <img src="<?php echo e(public_path('images/logo-siga.png')); ?>" alt="Logo SIGA" class="logo-siga">
            </td>
        </tr>
    </table>

    <div class="details">
        <span><strong>Automóvel:</strong> <?php echo e($vehicle->model); ?></span>
        <span><strong>Placa:</strong> <?php echo e($vehicle->license_plate); ?></span>
        <span><strong>Período (mês/ano):</strong> <?php echo e(\Carbon\Carbon::parse($startDate)->format('m/Y')); ?></span>
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
            <?php $__empty_1 = true; $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($trip->departure_datetime->format('d')); ?></td>
                    <td><?php echo e($trip->departure_datetime->format('H:i')); ?></td>
                    <td><?php echo e(number_format($trip->departure_odometer, 0, ',', '.')); ?></td>
                    <td><?php echo e($trip->arrival_datetime?->format('d')); ?></td>
                    <td><?php echo e($trip->arrival_datetime?->format('H:i')); ?></td>
                    <td><?php echo e($trip->arrival_odometer ? number_format($trip->arrival_odometer, 0, ',', '.') : ''); ?></td>
                    <td class="col-destino"><?php echo e($trip->destination); ?></td>
                    <td class="col-passageiros"><?php echo e($trip->passengers ?: 'N/A'); ?></td>
                    <td><?php echo e($trip->driver?->name ?? 'N/A'); ?></td>
                    <td><?php echo e($trip->guard_on_departure); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="10" style="padding: 15px;">Nenhuma viagem encontrada para este veículo no período
                        selecionado.</td>
                </tr>
            <?php endif; ?>
            
            <?php for($i = 0; $i < 20 - $trips->count(); $i++): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="footer">
        
        <div class="km-box">
            <span>KM(s) Percorridos/mês: <?php echo e(number_format($totalKm, 0, ',', '.')); ?></span>
        </div>

        <div class="signature">
            <strong>Assinatura responsável:</strong> _________________________
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\daniel.castro\Desktop\Projetos IFNMG\siga-if\resources\views/reports/pdf/official.blade.php ENDPATH**/ ?>