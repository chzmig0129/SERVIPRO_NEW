    <!-- Sección para Trampas con Mayor Captura -->
    <div class="page-break"></div>
    <div class="section">
        <h2>Trampas con Mayor Captura: <?= esc($plagaSeleccionada ?? 'No seleccionada') ?></h2>
        <div class="chart-container">
            <img src="<?= isset($trampasMayorCapturaImagen) ? 'data:image/png;base64,' . $trampasMayorCapturaImagen : '' ?>" alt="Gráfico de Trampas con Mayor Captura" class="chart-image">
        </div>
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
        <div class="notes">
            <h3>Notas:</h3>
            <p id="notas-areasCapturasPorPlaga"><?= esc($notas['areasCapturasPorPlagaChart'] ?? '') ?></p>
        </div>
    </div> 