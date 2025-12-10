<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Plantas</title>
    <style>
        @page {
            margin: 15mm;
            size: A4;
        }
        
        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            font-size: 11px;
            color: #2c3e50;
            background: white;
        }
        
        .container {
            background: white;
            padding: 0;
        }
        
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            margin-bottom: 15px;
            position: relative;
            border-bottom: 4px solid #3498db;
        }
        
        .logo {
            position: absolute;
            top: 15px;
            left: 20px;
            width: 80px;
            height: auto;
            z-index: 10;
        }
        
        .header-content {
            text-align: center;
            margin-left: 100px;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .sede-title {
            font-size: 18px;
            font-weight: 600;
            margin: 8px 0;
            color: #ecf0f1;
        }
        
        .header .date {
            font-size: 12px;
            margin-top: 8px;
            opacity: 0.9;
            font-style: italic;
        }
        
        .content {
            padding: 0;
        }
        
        .info-bar {
            background: #ecf0f1;
            border-left: 4px solid #3498db;
            padding: 12px 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-weight: 700;
            color: #2c3e50;
            font-size: 11px;
        }
        
        .summary-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .summary-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 12px;
            text-align: center;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .summary-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 12px;
            text-align: center;
            border-left: 4px solid #3498db;
        }
        
        .summary-number {
            font-size: 24px;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
            color: #2c3e50;
        }
        
        .summary-label {
            font-size: 10px;
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .charts-section {
            margin-top: 15px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            border: 1px solid #dee2e6;
            page-break-inside: avoid;
            min-height: 450px;
        }
        
        .chart-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #3498db;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .chart-image {
            max-width: 100%;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto 20px auto;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            object-fit: contain;
        }
        
        .chart-notes {
            background: #f8f9fa;
            border-left: 4px solid #3498db;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 11px;
            line-height: 1.6;
            color: #495057;
            border: 1px solid #e9ecef;
        }
        
        .chart-notes p {
            margin: 0 0 8px 0;
        }
        
        .chart-notes p:last-child {
            margin-bottom: 0;
        }
        
        .chart-notes strong {
            color: #2c3e50;
            font-size: 12px;
            font-weight: 700;
        }
        
        .actions-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        
        .actions-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
            text-align: center;
            color: #2c3e50;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .actions-content {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            padding: 12px;
            font-size: 10px;
            line-height: 1.4;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            font-size: 10px;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        .chart-break {
            page-break-inside: avoid;
        }
        
        @media print {
            body {
                background: white !important;
            }
            .chart-container {
                break-inside: avoid;
            }
        }
        
        /* Estilos para hacer el contenido más compacto */
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 8px;
        }
        
        p {
            margin: 0 0 8px 0;
        }
        
        .compact {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <!-- Logo de Servipro -->
            <img src="https://servipro.com.mx/assets/styles/public/general/logo.jpg" alt="Logo Servipro" class="logo">
            
            <div class="header-content">
                <h1>Reporte de Control de Plagas</h1>
                <p class="sede-title">
                    <?php if (!empty($nombre_sede_seleccionada)): ?>
                        <?= esc($nombre_sede_seleccionada) ?>
                    <?php else: ?>
                        Todas las Plantas
                    <?php endif; ?>
                </p>
                <p class="date">Generado el <?= date('d/m/Y H:i:s') ?></p>
            </div>
        </div>

        <div class="content">
            <!-- Barra de información compacta -->
            <div class="info-bar">
                <div class="info-item">
                    <span class="info-label">Planta</span>
                    <span class="info-value">
                        <?php if (!empty($nombre_sede_seleccionada)): ?>
                            <?= esc($nombre_sede_seleccionada) ?>
                        <?php else: ?>
                            Todas las Plantas
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Fecha</span>
                    <span class="info-value"><?= date('d/m/Y') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Hora</span>
                    <span class="info-value"><?= date('H:i:s') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Tipo</span>
                    <span class="info-value">Análisis Integral</span>
                </div>
            </div>
            
            <!-- Resumen General -->
            <div class="summary-section">
                <div class="summary-title">Resumen Ejecutivo</div>
                <div class="summary-grid">
                    <div class="summary-card">
                        <span class="summary-number"><?= number_format($totalTrampasSede) ?></span>
                        <span class="summary-label">Trampas Instaladas</span>
                    </div>
                    <div class="summary-card">
                        <span class="summary-number"><?= number_format($totalIncidencias) ?></span>
                        <span class="summary-label">Incidencias Registradas</span>
                    </div>
                    <div class="summary-card">
                        <span class="summary-number"><?= number_format($totalPlanos) ?></span>
                        <span class="summary-label">Planos Disponibles</span>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($chart_images)): ?>
            <!-- Sección de Gráficos -->
            <div class="charts-section">
                <h2 class="section-title">Análisis Gráfico</h2>
                
                <?php if (!empty($chart_images['plagasMayorPresencia'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">PLAGAS CON MAYOR PRESENCIA</h3>
                    <img src="<?= $chart_images['plagasMayorPresencia'] ?>" alt="Gráfico de plaga con mayor presencia" class="chart-image">
                    <?php if (!empty($notas_graficas['plagasMayorPresencia'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['plagasMayorPresencia'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <?php if (!empty($chart_images['areasMayorIncidencia'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">AREAS CON MAYOR INCIDENCIA</h3>
                    <img src="<?= $chart_images['areasMayorIncidencia'] ?>" alt="Gráfico de áreas con mayor incidencia" class="chart-image">
                    <?php if (!empty($notas_graficas['areasMayorIncidencia'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['areasMayorIncidencia'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <div class="page-break"></div>
                
                <?php if (!empty($chart_images['trampasPorUbicacion'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">DISTRIBUCION ESTRATEGICA DE TRAMPAS</h3>
                    <img src="<?= $chart_images['trampasPorUbicacion'] ?>" alt="Gráfico de trampas por ubicación" class="chart-image">
                    <?php if (!empty($notas_graficas['trampasPorUbicacion'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['trampasPorUbicacion'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($chart_images['incidenciasTipo'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">TENDENCIAS DE INCIDENCIAS POR PERIODO</h3>
                    <img src="<?= $chart_images['incidenciasTipo'] ?>" alt="Gráfico de incidencias por tipo" class="chart-image">
                    <?php if (!empty($notas_graficas['incidenciasTipo'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['incidenciasTipo'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($chart_images['trampasMayorCaptura'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">TRAMPAS CON MAYOR EFECTIVIDAD</h3>
                    <img src="<?= $chart_images['trampasMayorCaptura'] ?>" alt="Gráfico de trampas con mayor captura" class="chart-image">
                    <?php if (!empty($notas_graficas['trampasMayorCaptura'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['trampasMayorCaptura'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($chart_images['areasCapturasPorPlaga'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">DISTRIBUCION DE CAPTURAS POR AREA</h3>
                    <img src="<?= $chart_images['areasCapturasPorPlaga'] ?>" alt="Gráfico de áreas con capturas" class="chart-image">
                    <?php if (!empty($notas_graficas['areasCapturasPorPlaga'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['areasCapturasPorPlaga'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($chart_images['graficaTipoIncidencia'])): ?>
                <div class="chart-container chart-break">
                    <h3 class="chart-title">DISTRIBUCION POR TIPO DE INCIDENCIA</h3>
                    <img src="<?= $chart_images['graficaTipoIncidencia'] ?>" alt="Gráfico de distribución por tipo de incidencia" class="chart-image">
                    <?php if (!empty($notas_graficas['graficaTipoIncidencia'])): ?>
                    <div class="chart-notes">
                        <p><strong>OBSERVACIONES:</strong></p>
                        <p><?= nl2br(esc($notas_graficas['graficaTipoIncidencia'])) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Acciones de Seguimiento -->
            <?php if (!empty($acciones_seguimiento)): ?>
            <div class="actions-section">
                <div class="actions-title">PLAN DE ACCIONES DE SEGUIMIENTO</div>
                <div class="actions-content">
                    <p style="white-space: pre-wrap; margin: 0; line-height: 1.6;"><?= nl2br(esc($acciones_seguimiento)) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>ServiPro &copy; <?= date('Y') ?> </p>
        </div>
    </div>
</body>
</html> 