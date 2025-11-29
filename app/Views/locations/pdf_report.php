    <!-- Sección para Trampas con Mayor Captura -->
    <div class="page-break"></div>
    <div class="section">
        <h2>Trampas con Mayor Captura: <?= esc($plagaSeleccionada ?? 'No seleccionada') ?></h2>
        <div class="chart-container">
            <img src="<?= isset($trampasMayorCapturaImagen) ? 'data:image/png;base64,' . $trampasMayorCapturaImagen : '' ?>" alt="Gráfico de Trampas con Mayor Captura" class="chart-image">
        </div>
        
        <!-- Tabla explicativa para Trampas con Mayor Captura -->
        <?php if (!empty($trampasMayorCaptura)): ?>
        <div class="table-container" style="margin-top: 20px;">
            <h3>Detalle de Capturas por Trampa</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">ID Trampa</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Ubicación</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Organismos</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Última Captura</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trampasMayorCaptura as $trampa): ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($trampa['id_trampa']) ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($trampa['ubicacion']) ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= number_format($trampa['total_organismos']) ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= date('d/m/Y', strtotime($trampa['ultima_captura'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="notes">
            <h3>Notas:</h3>
            <p id="notas-trampasMayorCaptura"><?= esc($notas['trampasMayorCapturaChart'] ?? '') ?></p>
        </div>
    </div> 

    <!-- Sección para Áreas que Presentaron Capturas -->
    <div class="page-break"></div>
    <div class="section">
        <h2>Áreas que Presentaron Capturas</h2>
        <div class="chart-container">
            <img src="<?= isset($areasCapturasPorPlagaImagen) ? 'data:image/png;base64,' . $areasCapturasPorPlagaImagen : '' ?>" alt="Gráfico de Áreas que Presentaron Capturas" class="chart-image">
        </div>
        
        <!-- Tabla explicativa para Áreas con Capturas -->
        <?php if (!empty($areasCapturasPorPlaga)): ?>
        <div class="table-container" style="margin-top: 20px;">
            <h3>Detalle de Capturas por Área</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Área</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Total Capturas</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Porcentaje</th>
                        <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">Nivel de Riesgo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalCapturas = array_sum(array_column($areasCapturasPorPlaga, 'total_capturas'));
                    foreach ($areasCapturasPorPlaga as $area): 
                        $porcentaje = ($totalCapturas > 0) ? round(($area['total_capturas'] / $totalCapturas) * 100, 1) : 0;
                        $nivelRiesgo = $porcentaje > 30 ? 'Alto' : ($porcentaje > 15 ? 'Medio' : 'Bajo');
                    ?>
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= esc($area['ubicacion']) ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= number_format($area['total_capturas']) ?></td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= $porcentaje ?>%</td>
                        <td style="border: 1px solid #ddd; padding: 8px;"><?= $nivelRiesgo ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="notes">
            <h3>Notas:</h3>
            <p id="notas-areasCapturasPorPlaga"><?= esc($notas['areasCapturasPorPlagaChart'] ?? '') ?></p>
        </div>
    </div> 