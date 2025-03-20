<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-7xl mx-auto px-4">
    <!-- Encabezado con selector y botón de reporte -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard de Sedes</h1>
            <p class="text-gray-500">Análisis y métricas detalladas por sede</p>
        </div>
        <div class="flex flex-col md:flex-row gap-3">
                <select id="sede-selector" name="sede_id" class="w-full md:w-64 p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" onchange="cambiarSede(this.value)">
                <?php if(empty($sedes)): ?>
                    <option>No hay sedes disponibles</option>
                <?php else: ?>
                    <option value="">Seleccione una sede</option>
                    <?php foreach($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>" <?= ($sedeSeleccionada == $sede['id']) ? 'selected' : '' ?>><?= esc($sede['nombre']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
                <button onclick="descargarPDF()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-download">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/>
        <line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
    Descargar Reporte (PDF)
</button>
            </div>
        </div>
    </div>

    <!-- Mostrar mensaje de error si existe -->
    <?php if(isset($mensaje_error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg" role="alert">
        <p><?= $mensaje_error ?></p>
    </div>
    <?php endif; ?>

    <!-- Grid de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Tarjeta: Total de Trampas -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total de Trampas en la Sede</h3>
            <p class="text-3xl font-bold text-blue-600"><?= $totalTrampasSede; ?></p>
            <p class="text-sm text-gray-500">trampas instaladas</p>
        </div>
        
        <!-- Tarjeta: Total de Incidencias -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total de Incidencias en la Sede</h3>
            <p class="text-3xl font-bold text-amber-600">
                <?php 
                    $sumaTotal = array_sum(array_column($totalIncidenciasPorTipo, 'total'));
                    echo $sumaTotal;
                ?>
            </p>
            <p class="text-sm text-gray-500">incidencias registradas</p>
        </div>
        
        <!-- Tarjeta: Planos Disponibles -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Planos Disponibles</h3>
            <p class="text-3xl font-bold text-green-600"><?= count($planos); ?></p>
            <p class="text-sm text-gray-500">planos de ubicación</p>
        </div>
    </div>

    <!-- Detalle de Trampas -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detalle de Trampas en la Sede</h3>
        
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-white border border-gray-300">
        <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">ID</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Trampa</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Ubicación</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trampasDetalle as $trampa): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border"><?= $trampa['id']; ?></td>
                            <td class="py-3 px-4 border"><?= $trampa['tipo']; ?></td>
                            <td class="py-3 px-4 border"><?= $trampa['ubicacion']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
    </div>

    <!-- Planos de la Sede -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Mapas de calor por plano</h3>
        
        <?php if (empty($planos)): ?>
            <p class="text-gray-500 italic text-center py-4">No hay planos disponibles para esta sede.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($planos as $plano): ?>
                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all">
                        <div class="relative h-48 overflow-hidden">
                            <?php if (!empty($plano['preview_image'])): ?>
                                <img src="<?= $plano['preview_image'] ?>" alt="<?= esc($plano['nombre']) ?>" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-lg text-gray-800"><?= esc($plano['nombre']) ?></h4>
                            <p class="text-sm text-gray-600 line-clamp-2 mt-1"><?= esc($plano['descripcion']) ?></p>
                            <div class="mt-4 flex justify-end">
                                <a href="<?= base_url('blueprints/verImagen/' . $plano['id']) ?>" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Ver imagen
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
</div>

    <!-- Incidencias -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detalle de Incidencias</h3>
        
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-white border border-gray-300">
        <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Incidencia</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Plaga</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Cantidad de Organismos</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Insecto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($totalIncidenciasPorTipo as $incidencia): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['tipo_incidencia']); ?></td>
                            <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['tipo_plaga']); ?></td>
                            <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['cantidad_organismos'] ?? 0); ?></td>
                            <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['tipo_insecto'] ?? 0); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Nuevas gráficas -->
        <!-- Gráfico: Plagas con Mayor Presencia -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Plaga con Mayor Presencia durante el Mes</h3>
                <select id="mes-selector" class="w-full md:w-64 p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                        onchange="cambiarMes(this.value)">
                    <?php if(empty($listaMeses)): ?>
                        <option>No hay datos disponibles</option>
                    <?php else: ?>
                        <?php foreach($listaMeses as $mes): ?>
                            <option value="<?= $mes['mes_valor'] ?>" <?= ($mesSeleccionado == $mes['mes_valor']) ? 'selected' : '' ?>><?= esc($mes['mes_nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="width: 100%; height: 400px;">
                <canvas id="plagasMayorPresenciaChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-plagas" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="plagasMayorPresenciaChart"></textarea>
            </div>
        </div>

        <!-- Gráfico: Áreas con Mayor Incidencia de la Plaga Seleccionada -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Áreas con Mayor Incidencia de Plaga</h3>
                <select id="plaga-selector" class="w-full md:w-64 p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                        onchange="cambiarPlaga(this.value)">
                    <?php if(empty($listaPlagas)): ?>
                        <option>No hay plagas disponibles</option>
                    <?php else: ?>
                        <?php foreach($listaPlagas as $plaga): ?>
                            <option value="<?= $plaga['plaga'] ?>" <?= ($plagaSeleccionada == $plaga['plaga']) ? 'selected' : '' ?>><?= esc($plaga['plaga']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="width: 100%; height: 400px;">
                <canvas id="areasMayorIncidenciaChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-areas" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="areasMayorIncidenciaChart"></textarea>
            </div>
        </div>

        <!-- Gráficos existentes -->
        <!-- Gráfico: Incidencias por Tipo y Mes -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Incidencias por Tipo y Mes</h3>
            <div style="width: 100%; height: 400px;">
                <canvas id="incidenciasTipoChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-1" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="incidenciasTipoChart"></textarea>
            </div>
        </div>

        <!-- Gráfico: Distribución de Trampas por Ubicación -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Distribución de Trampas por Ubicación</h3>
            <div style="width: 100%; height: 400px;">
                <canvas id="trampasPorUbicacionChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-2" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="trampasPorUbicacionChart"></textarea>
            </div>
        </div>

        <!-- Gráfico: Incidencias por Tipo de Plaga -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Incidencias por Tipo de Plaga</h3>
            <div style="width: 100%; height: 400px;">
                <canvas id="incidenciasPlagaChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-3" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="incidenciasPlagaChart"></textarea>
            </div>
        </div>
        
        <!-- Gráfico: Trampas con Mayor Captura por Plaga -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Trampas con Mayor Captura</h3>
                <select id="plaga-captura-selector" class="w-full md:w-64 p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" 
                        onchange="cambiarPlagaCaptura(this.value)">
                    <?php if(empty($listaPlagas)): ?>
                        <option>No hay plagas disponibles</option>
                    <?php else: ?>
                        <?php foreach($listaPlagas as $plaga): ?>
                            <option value="<?= $plaga['plaga'] ?>" <?= ($plagaSeleccionada == $plaga['plaga']) ? 'selected' : '' ?>><?= esc($plaga['plaga']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div style="width: 100%; height: 500px;">
                <canvas id="trampasMayorCapturaChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-captura" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="trampasMayorCapturaChart"></textarea>
            </div>
        </div>

        <!-- Gráfico: Áreas que Presentaron Capturas por Plaga -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Áreas que Presentaron Capturas</h3>
            <div style="width: 100%; height: 500px;">
                <canvas id="areasCapturasPorPlagaChart"></canvas>
            </div>
            
            <!-- Notas para este gráfico -->
            <div class="mt-4 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Notas para este gráfico</h4>
                <textarea id="notas-grafico-areas-capturas" class="w-full h-24 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                    placeholder="Añade tus notas aquí..."
                    data-grafico="areasCapturasPorPlagaChart"></textarea>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones de seguimiento -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Acciones de Seguimiento</h3>
        <p class="text-gray-600 mb-4">Registre aquí las acciones de seguimiento para este informe. Esta información se incluirá en el reporte final.</p>
        
        <div class="border border-gray-200 rounded-lg p-4">
            <textarea id="acciones-seguimiento" class="w-full h-48 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                placeholder="Detalle aquí las acciones de seguimiento propuestas, responsables y fechas estimadas de ejecución..."></textarea>
        </div>
    </div>

    <!-- Botón para generar PDF con todas las tablas y gráficas -->
    <div class="mt-8 mb-12 text-center">
        <button id="generarPdfBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M9 15v-2"/><path d="M12 15v-6"/><path d="M15 15v-4"/></svg>
            Generar Informe Completo
        </button>
    </div>
</div>

<!-- Mantener todos los scripts originales -->
<script>
function cambiarSede(sedeId) {
    if (sedeId) {
        console.log('Cambiando a sede: ' + sedeId);
        window.location.href = '<?= base_url('locations') ?>?sede_id=' + sedeId;
    } else {
        console.log('No se seleccionó ninguna sede');
    }
}

function descargarPDF() {
    // Mostrar indicador de carga
    const loadingIndicator = document.createElement('div');
    loadingIndicator.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loadingIndicator.innerHTML = `
        <div class="bg-white p-6 rounded-lg shadow-lg text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
            <p class="mt-4">Generando PDF, por favor espere...</p>
        </div>
    `;
    document.body.appendChild(loadingIndicator);
    
    setTimeout(async function() {
        try {
            // Inicializar jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('p', 'mm', 'a4');
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const margin = 10;
            
            // Obtener información de la sede
            const sedeSelector = document.getElementById('sede-selector');
            const nombreSede = sedeSelector.options[sedeSelector.selectedIndex].text;
            const fechaActual = new Date().toLocaleDateString('es-ES');
            
            // Configurar encabezado del documento
            doc.setFontSize(18);
            doc.setTextColor(44, 62, 80);
            doc.text('Reporte de Sede', pageWidth / 2, margin + 10, { align: 'center' });
            
            doc.setFontSize(14);
            doc.text(nombreSede, pageWidth / 2, margin + 20, { align: 'center' });
            
            doc.setFontSize(10);
            doc.text(`Generado el: ${fechaActual}`, pageWidth / 2, margin + 30, { align: 'center' });
            
            // Añadir resumen de datos
            doc.setFontSize(12);
            doc.setTextColor(52, 73, 94);
            doc.text('Resumen de Datos', margin, margin + 45);
            
            // Obtener y limpiar datos de trampas, incidencias y planos
            let totalTrampas = document.querySelector('.text-3xl.font-bold.text-blue-600').textContent.trim();
            let totalIncidencias = document.querySelector('.text-3xl.font-bold.text-amber-600').textContent.trim();
            let totalPlanos = document.querySelector('.text-3xl.font-bold.text-green-600').textContent.trim();
            
            // Eliminar cualquier salto de línea o espacio adicional
            totalTrampas = totalTrampas.replace(/\s+/g, ' ');
            totalIncidencias = totalIncidencias.replace(/\s+/g, ' ');
            totalPlanos = totalPlanos.replace(/\s+/g, ' ');
            
            doc.setFontSize(10);
            doc.text(`• Total de Trampas: ${totalTrampas}`, margin + 5, margin + 55);
            doc.text(`• Total de Incidencias: ${totalIncidencias}`, margin + 5, margin + 65);
            doc.text(`• Planos Disponibles: ${totalPlanos}`, margin + 5, margin + 75);
            
            let yPos = margin + 90;
            
            // Obtener tablas
            const tables = document.querySelectorAll('table');
            
            // Tabla de Trampas
            doc.setFontSize(12);
            doc.text('Detalle de Trampas', margin, yPos);
            yPos += 10;
            
            // Convertir primera tabla (trampas) a formato jspdf-autotable
            const trampaHeaders = [];
            const trampaRows = [];
            
            // Obtener encabezados
            tables[0].querySelectorAll('thead th').forEach(th => {
                trampaHeaders.push(th.textContent);
            });
            
            // Obtener filas
            tables[0].querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.textContent);
                });
                trampaRows.push(row);
            });
            
            // Añadir tabla de trampas
            doc.autoTable({
                startY: yPos,
                head: [trampaHeaders],
                body: trampaRows,
                theme: 'grid',
                headStyles: { fillColor: [41, 128, 185], textColor: 255 },
                margin: { top: 10, right: margin, bottom: 10, left: margin },
                styles: { overflow: 'linebreak', cellPadding: 3 }
            });
            
            yPos = doc.lastAutoTable.finalY + 15;
            
            // Añadir nueva página si no hay suficiente espacio
            if (yPos > pageHeight - 40) {
                doc.addPage();
                yPos = margin + 10;
            }
            
            // Tabla de Incidencias
            doc.setFontSize(12);
            doc.text('Detalle de Incidencias', margin, yPos);
            yPos += 10;
            
            // Convertir segunda tabla (incidencias) a formato jspdf-autotable
            const incidenciaHeaders = [];
            const incidenciaRows = [];
            
            // Obtener encabezados
            tables[1].querySelectorAll('thead th').forEach(th => {
                incidenciaHeaders.push(th.textContent);
            });
            
            // Obtener filas
            tables[1].querySelectorAll('tbody tr').forEach(tr => {
                const row = [];
                tr.querySelectorAll('td').forEach(td => {
                    row.push(td.textContent);
                });
                incidenciaRows.push(row);
            });
            
            // Añadir tabla de incidencias
            doc.autoTable({
                startY: yPos,
                head: [incidenciaHeaders],
                body: incidenciaRows,
                theme: 'grid',
                headStyles: { fillColor: [211, 84, 0], textColor: 255 },
                margin: { top: 10, right: margin, bottom: 10, left: margin },
                styles: { overflow: 'linebreak', cellPadding: 3 }
            });
            
            yPos = doc.lastAutoTable.finalY + 15;
            
            // Añadir gráficos
            const charts = [
                { id: 'plagasMayorPresenciaChart', title: 'Plaga con Mayor Presencia durante ' + document.getElementById('mes-selector').options[document.getElementById('mes-selector').selectedIndex].text },
                { id: 'areasMayorIncidenciaChart', title: 'Áreas con Mayor Incidencia de Plaga' },
                { id: 'incidenciasTipoChart', title: 'Incidencias por Tipo y Mes' },
                { id: 'trampasPorUbicacionChart', title: 'Distribución de Trampas por Ubicación' },
                { id: 'incidenciasPlagaChart', title: 'Incidencias por Tipo de Plaga' },
                { id: 'areasCapturasPorPlagaChart', title: 'Áreas que Presentaron Capturas' }
            ];
            
            for (const chart of charts) {
                // Añadir nueva página para cada gráfico
                doc.addPage();
                yPos = margin + 10;
                
                // Título del gráfico
                doc.setFontSize(12);
                doc.text(chart.title, margin, yPos);
                yPos += 10;
                
                // Capturar el gráfico como imagen
                const canvas = document.getElementById(chart.id);
                if (canvas) {
                    const imgData = canvas.toDataURL('image/png');
                    
                    // Calcular dimensiones para mantener la proporción (reducidas a la mitad)
                    const canvasRatio = canvas.width / canvas.height;
                    const imgWidth = (pageWidth - (margin * 2)) * 0.5; // Reducir al 50%
                    const imgHeight = imgWidth / canvasRatio;
                    
                    // Calcular posición para centrar la imagen
                    const xPos = (pageWidth - imgWidth) / 2;
                    
                    // Añadir imagen del gráfico centrada
                    doc.addImage(imgData, 'PNG', xPos, yPos, imgWidth, imgHeight);
                    
                    // Obtener notas del gráfico
                    const notasTextarea = document.querySelector(`textarea[data-grafico="${chart.id}"]`);
                    if (notasTextarea && notasTextarea.value.trim()) {
                        yPos += imgHeight + 10;
                        
                        // Añadir sección de notas
                        doc.setFontSize(10);
                        doc.text('Notas:', margin, yPos);
                        yPos += 5;
                        
                        const notas = notasTextarea.value;
                        const splitText = doc.splitTextToSize(notas, pageWidth - (margin * 2));
                        doc.text(splitText, margin, yPos);
                    }
                }
            }
            
            // Añadir página para acciones de seguimiento
            doc.addPage();
            yPos = margin + 10;
            
            // Título de acciones de seguimiento
            doc.setFontSize(14);
            doc.setTextColor(44, 62, 80);
            doc.text('ACCIONES DE SEGUIMIENTO', pageWidth / 2, yPos, { align: 'center' });
            yPos += 15;
            
            // Obtener acciones de seguimiento
            const accionesSeguimiento = document.getElementById('acciones-seguimiento').value;
            
            if (accionesSeguimiento.trim()) {
                doc.setFontSize(10);
                doc.setTextColor(52, 73, 94);
                const splitText = doc.splitTextToSize(accionesSeguimiento, pageWidth - (margin * 2));
                doc.text(splitText, margin, yPos);
                yPos += splitText.length * 5 + 20; // Ajustar posición para firmas
            } else {
                doc.setFontSize(10);
                doc.setTextColor(150, 150, 150);
                doc.text('No se registraron acciones de seguimiento.', margin, yPos);
                yPos += 30; // Ajustar posición para firmas
            }
            
            // Añadir sección de firmas
            const anchoFirma = (pageWidth - (margin * 3)) / 2;
            
            // Dibujar líneas para firmas
            doc.setDrawColor(150, 150, 150);
            doc.setLineWidth(0.5);
            
            // Firma 1: Supervisor Técnico
            doc.line(margin, yPos, margin + anchoFirma, yPos);
            doc.setFontSize(10);
            doc.setTextColor(52, 73, 94);
            doc.text('Supervisor Técnico', margin + (anchoFirma / 2), yPos + 5, { align: 'center' });
            doc.text('ServiPro México', margin + (anchoFirma / 2), yPos + 10, { align: 'center' });
            
            // Firma 2: Responsable de Sede
            doc.line(margin * 2 + anchoFirma, yPos, pageWidth - margin, yPos);
            doc.text('Responsable de la Sede', margin * 2 + anchoFirma + (anchoFirma / 2), yPos + 5, { align: 'center' });
            doc.text(nombreSede, margin * 2 + anchoFirma + (anchoFirma / 2), yPos + 10, { align: 'center' });
            
            
            // Añadir pie de página con número de página
            const totalPages = doc.internal.getNumberOfPages();
            for (let i = 1; i <= totalPages; i++) {
                doc.setPage(i);
                doc.setFontSize(8);
                doc.setTextColor(150);
                doc.text(`Página ${i} de ${totalPages}`, pageWidth - margin, pageHeight - 10);
            }
            
            // Guardar el PDF
            const pdfName = `Reporte_${nombreSede.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.pdf`;
            doc.save(pdfName);
            
        } catch (error) {
            console.error('Error al generar el PDF:', error);
            alert('Ocurrió un error al generar el PDF. Por favor, inténtelo de nuevo.');
        } finally {
            // Eliminar indicador de carga
            document.body.removeChild(loadingIndicator);
        }
    }, 100);
}

// Verificar si el selector de sedes existe y está funcionando correctamente
document.addEventListener('DOMContentLoaded', function() {
    const sedeSelector = document.getElementById('sede-selector');
    if (sedeSelector) {
        console.log('Selector de sedes cargado correctamente');
        console.log('Valor actual: ' + sedeSelector.value);
    } else {
        console.error('Error: No se encontró el selector de sedes');
    }
});
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP para trampas por ubicación
        const trampasPorUbicacion = <?= json_encode($trampasPorUbicacion ?? []); ?>;

        // Preparar datos para el gráfico
        const ubicaciones = trampasPorUbicacion.map(item => item.ubicacion);
        const totales = trampasPorUbicacion.map(item => parseInt(item.total));

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('trampasPorUbicacionChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'trampasPorUbicacionChart'");
            return;
        }

        // Generar gradiente para las barras
        const ctx = canvas.getContext('2d');
        const gradiente = ctx.createLinearGradient(0, 0, 0, 400);
        gradiente.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
        gradiente.addColorStop(1, 'rgba(54, 162, 235, 0.4)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ubicaciones,
                datasets: [{
                    label: 'Número de Trampas',
                    data: totales,
                    backgroundColor: gradiente,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                    hoverBackgroundColor: 'rgba(54, 162, 235, 0.9)',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 15,
                        right: 25,
                        top: 25,
                        bottom: 15
                    }
                },
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                        align: 'center',
                        labels: {
                            boxWidth: 15,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'rect',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCIÓN DE TRAMPAS POR UBICACIÓN',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: Math.round,
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold',
                            size: 12
                        },
                        color: 'rgba(54, 162, 235, 1)',
                        offset: 6
                    }
                },
                scales: {
                    x: { 
                        title: { 
                            display: true, 
                            text: 'Ubicación',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            padding: {top: 10, bottom: 0}
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 10,
                                weight: 'bold'
                            },
                            padding: 5,
                            color: '#6b7280'
                        }
                    },
                    y: { 
                        beginAtZero: true, 
                        title: { 
                            display: true, 
                            text: 'Número de Trampas',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            padding: {top: 0, bottom: 10}
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11
                            },
                            padding: 8,
                            color: '#6b7280'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>

<!-- Cargar Chart.js y el plugin para mostrar porcentajes -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const incidenciasPorTipoPlaga = <?= json_encode($incidenciasPorTipoPlaga ?? []); ?>;

        let incidenciasMap = {};

        // Procesar los datos para agrupar por tipo de plaga
        incidenciasPorTipoPlaga.forEach(item => {
            let plaga = item.tipo_plaga || "Desconocida";
            let total = parseInt(item.total, 10) || 0;

            if (!incidenciasMap[plaga]) {
                incidenciasMap[plaga] = 0;
            }
            incidenciasMap[plaga] += total;
        });

        // Extraer datos para el gráfico
        let etiquetas = Object.keys(incidenciasMap);
        let valores = etiquetas.map(plaga => incidenciasMap[plaga]);

        // Definir colores personalizados para mejorar el aspecto visual
        const colores = [
            'rgba(37, 99, 235, 0.8)',   // Azul
            'rgba(234, 88, 12, 0.8)',   // Naranja
            'rgba(22, 163, 74, 0.8)',   // Verde
            'rgba(217, 70, 239, 0.8)',  // Morado
            'rgba(225, 29, 72, 0.8)',   // Rojo
            'rgba(15, 118, 110, 0.8)',  // Verde azulado
            'rgba(124, 58, 237, 0.8)',  // Violeta
            'rgba(245, 158, 11, 0.8)',  // Ámbar
            'rgba(6, 182, 212, 0.8)',   // Cian
            'rgba(79, 70, 229, 0.8)',   // Índigo
            'rgba(236, 72, 153, 0.8)',  // Rosa
            'rgba(76, 29, 149, 0.8)'    // Violeta oscuro
        ];

        // Si hay más etiquetas que colores, generar adicionales
        if (etiquetas.length > colores.length) {
            for (let i = colores.length; i < etiquetas.length; i++) {
                const r = Math.floor(Math.random() * 255);
                const g = Math.floor(Math.random() * 255);
                const b = Math.floor(Math.random() * 255);
                colores.push(`rgba(${r}, ${g}, ${b}, 0.8)`);
            }
        }

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('incidenciasPlagaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'incidenciasPlagaChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: etiquetas,
                datasets: [{
                    data: valores,
                    backgroundColor: colores,
                    borderColor: colores.map(color => color.replace('0.8', '1')),
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    borderRadius: 5,
                    spacing: 4,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '40%',  // Para hacer una gráfica de tipo donut
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: { 
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 15,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCIÓN DE INCIDENCIAS POR TIPO DE PLAGA',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            let total = ctx.chart.data.datasets[0].data.reduce((acc, val) => acc + val, 0);
                            let porcentaje = ((value / total) * 100).toFixed(1) + "%";
                            return porcentaje;
                        },
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold',
                            size: 12
                        },
                        textStrokeColor: 'rgba(0, 0, 0, 0.5)',
                        textStrokeWidth: 2,
                        textShadowBlur: 3,
                        textShadowColor: 'rgba(0, 0, 0, 0.5)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.chart.data.datasets[0].data.reduce((acc, data) => acc + data, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        displayColors: true,
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>

<!-- Cargar bibliotecas necesarias -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Guardar las notas en localStorage cuando se escriban
        document.querySelectorAll('textarea[id^="notas-grafico-"]').forEach(textarea => {
            // Cargar notas guardadas previamente
            const graficoId = textarea.dataset.grafico;
            const notasGuardadas = localStorage.getItem(`notas-${graficoId}`);
            if (notasGuardadas) {
                textarea.value = notasGuardadas;
            }
            
            // Guardar notas al escribir
            textarea.addEventListener('input', function() {
                localStorage.setItem(`notas-${graficoId}`, this.value);
            });
        });
        
        // Cargar y guardar acciones de seguimiento
        const accionesSeguimiento = document.getElementById('acciones-seguimiento');
        if (accionesSeguimiento) {
            // Cargar acciones guardadas previamente
            const accionesGuardadas = localStorage.getItem('acciones-seguimiento');
            if (accionesGuardadas) {
                accionesSeguimiento.value = accionesGuardadas;
            }
            
            // Guardar acciones al escribir
            accionesSeguimiento.addEventListener('input', function() {
                localStorage.setItem('acciones-seguimiento', this.value);
            });
        }
        
        // Configurar botón para generar PDF
        document.getElementById('generarPdfBtn').addEventListener('click', function() {
            descargarPDF();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const incidenciasPorTipoIncidencia = <?= json_encode($incidenciasPorTipoIncidencia ?? []); ?>;

        let incidenciasMap = {};
        let mesesSet = new Set();

        // Procesar los datos para agrupar por tipo de incidencia y mes
        incidenciasPorTipoIncidencia.forEach(item => {
            let mes = item.mes;
            let tipo = item.tipo_incidencia || "Desconocido";
            let total = parseInt(item.total, 10) || 0;

            if (!incidenciasMap[tipo]) {
                incidenciasMap[tipo] = {};
            }
            incidenciasMap[tipo][mes] = total;
            mesesSet.add(mes);
        });

        // Convertir meses en array ordenado
        let mesesOrdenados = Array.from(mesesSet).sort();

        // Crear datasets por tipo de incidencia
        let datasets = Object.keys(incidenciasMap).map(tipo => {
            return {
                label: tipo,
                data: mesesOrdenados.map(mes => incidenciasMap[tipo][mes] || 0),
                borderWidth: 1,
                backgroundColor: getRandomColor()
            };
        });

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('incidenciasTipoChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'incidenciasTipoChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: mesesOrdenados,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    x: { title: { display: true, text: 'Mes' } },
                    y: { beginAtZero: true, title: { display: true, text: 'Número de Incidencias' } }
                }
            }
        });

        // Función para generar colores aleatorios
        function getRandomColor() {
            return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`;
        }
    });
</script>

<style>
/* Estilos para mejorar la apariencia */
.bg-white {
    transition: all 0.3s ease;
}

.bg-white:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    border-radius: 0.5rem;
    overflow: hidden;
}

th {
    background-color: #f9fafb;
    font-weight: 600;
}

tr:hover {
    background-color: #f9fafb;
}

canvas {
    border-radius: 0.5rem;
}

textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
}

/* Nuevos estilos mejorados */
body {
    background-color: #f5f7fa;
    color: #2d3748;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.bg-white {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.bg-white:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

h1, h2, h3, h4 {
    font-weight: 700;
    color: #1a202c;
}

h1 {
    font-size: 1.875rem;
    letter-spacing: -0.025em;
}

h3 {
    position: relative;
    padding-bottom: 0.5rem;
}

h3:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 40px;
    background-color: #3b82f6;
    border-radius: 3px;
}

select, button {
    transition: all 0.2s ease;
}

select {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1em;
}

select:hover {
    border-color: #a0aec0;
}

table {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

th {
    background-color: #edf2f7;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #4a5568;
    padding: 1rem 0.75rem;
}

td {
    padding: 0.75rem;
    border-bottom: 1px solid #edf2f7;
}

tr:last-child td {
    border-bottom: none;
}

.rounded-lg {
    border-radius: 0.75rem;
}

textarea {
    resize: vertical;
    min-height: 80px;
    background-color: #f9fafc;
    transition: all 0.2s ease;
}

textarea:hover {
    background-color: #f8faff;
}

button {
    position: relative;
    overflow: hidden;
}

button:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(-100%);
    transition: transform 0.3s ease-out;
}

button:hover:after {
    transform: translateX(0);
}

/* Estilos para las tarjetas de resumen */
.grid-cols-3 .bg-white {
    position: relative;
    overflow: hidden;
}

.grid-cols-3 .bg-white:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
}

.grid-cols-3 .bg-white:nth-child(1):before {
    background-color: #3b82f6;
}

.grid-cols-3 .bg-white:nth-child(2):before {
    background-color: #f59e0b;
}

.grid-cols-3 .bg-white:nth-child(3):before {
    background-color: #10b981;
}

/* Estilos para los gráficos */
canvas {
    padding: 0.5rem;
    background-color: #ffffff;
}

/* Mejora para los botones de generación de reportes */
#generarPdfBtn {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
    border: none;
    font-weight: 600;
    letter-spacing: 0.5px;
}

#generarPdfBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
}

/* Estilo para tooltip */
[data-tooltip] {
    position: relative;
}

[data-tooltip]:after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 130%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #1e293b;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    white-space: nowrap;
    z-index: 10;
}

[data-tooltip]:hover:after {
    opacity: 1;
    visibility: visible;
}
</style>

<!-- Script para cambiar la plaga seleccionada -->
<script>
    function cambiarPlaga(plaga) {
        if (plaga) {
            console.log('Cambiando a plaga: ' + plaga);
            // Construir URL manteniendo el sede_id si existe
            const urlParams = new URLSearchParams(window.location.search);
            const sedeId = urlParams.get('sede_id');
            const mes = urlParams.get('mes');
            
            let url = '<?= base_url('locations') ?>?plaga=' + encodeURIComponent(plaga);
            
            if (sedeId) {
                url += '&sede_id=' + sedeId;
            }
            
            if (mes) {
                url += '&mes=' + mes;
            }
            
            window.location.href = url;
        } else {
            console.log('No se seleccionó ninguna plaga');
        }
    }
    
    function cambiarPlagaCaptura(plaga) {
        // Usar la misma función que cambiarPlaga, ya que ambas usan el mismo parámetro
        cambiarPlaga(plaga);
    }
    
    function cambiarMes(mes) {
        if (mes) {
            console.log('Cambiando a mes: ' + mes);
            // Construir URL manteniendo el sede_id y plaga si existen
            const urlParams = new URLSearchParams(window.location.search);
            const sedeId = urlParams.get('sede_id');
            const plaga = urlParams.get('plaga');
            
            let url = '<?= base_url('locations') ?>?mes=' + encodeURIComponent(mes);
            
            if (sedeId) {
                url += '&sede_id=' + sedeId;
            }
            
            if (plaga) {
                url += '&plaga=' + encodeURIComponent(plaga);
            }
            
            window.location.href = url;
        } else {
            console.log('No se seleccionó ningún mes');
        }
    }
</script>

<!-- Script para gráfica de Plagas con Mayor Presencia -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const plagasMayorPresencia = <?= json_encode($plagasMayorPresencia ?? []); ?>;
        const mesSeleccionado = "<?= $mesSeleccionado ?? ''; ?>";
        
        // Obtener el nombre del mes seleccionado para mostrar en el título
        let nombreMesSeleccionado = "";
        const selectMes = document.getElementById('mes-selector');
        if (selectMes && selectMes.selectedIndex >= 0) {
            nombreMesSeleccionado = selectMes.options[selectMes.selectedIndex].text;
        }

        // Verificar si hay datos
        if (!plagasMayorPresencia || plagasMayorPresencia.length === 0) {
            console.log('No hay datos de plagas con mayor presencia');
            return;
        }

        // Preparar datos para el gráfico
        const labels = plagasMayorPresencia.map(item => item.tipo_plaga || 'Desconocida');
        const data = plagasMayorPresencia.map(item => parseInt(item.total_organismos) || 0);
        
        // Generar colores para cada segmento
        const colors = generateColorPalette(labels.length);

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('plagasMayorPresenciaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'plagasMayorPresenciaChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.6', '1')),
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    borderRadius: 3,
                    spacing: 5,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '30%',
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: { 
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    title: {
                        display: true,
                        text: 'Plaga con Mayor Presencia durante ' + nombreMesSeleccionado,
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value * 100) / sum) + '%';
                            return percentage;
                        },
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold',
                            size: 12
                        },
                        textStrokeColor: 'rgba(0, 0, 0, 0.5)',
                        textStrokeWidth: 2,
                        textShadowBlur: 10,
                        textShadowColor: 'rgba(0, 0, 0, 0.5)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value * 100) / total);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        titleAlign: 'center',
                        bodyAlign: 'center'
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>

<!-- Script para gráfica de Áreas con Mayor Incidencia -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const areasMayorIncidencia = <?= json_encode($areasMayorIncidencia ?? []); ?>;
        const plagaSeleccionada = "<?= $plagaSeleccionada ?? 'No seleccionada'; ?>";

        // Verificar si hay datos
        if (!areasMayorIncidencia || areasMayorIncidencia.length === 0) {
            console.log('No hay datos de áreas con mayor incidencia para la plaga: ' + plagaSeleccionada);
            return;
        }

        // Preparar datos para el gráfico
        const labels = areasMayorIncidencia.map(item => item.ubicacion || 'Sin ubicación');
        const data = areasMayorIncidencia.map(item => parseInt(item.total_organismos) || 0);
        
        // Generar colores para cada segmento
        const colors = generateColorPalette(labels.length);

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('areasMayorIncidenciaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'areasMayorIncidenciaChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderColor: colors.map(color => color.replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            padding: 15
                        }
                    },
                    title: {
                        display: true,
                        text: 'Áreas con Mayor Incidencia de ' + plagaSeleccionada,
                        font: {
                            size: 16
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                            let percentage = Math.round((value * 100) / sum) + '%';
                            return percentage;
                        },
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    });

    // Función para generar paleta de colores
    function generateColorPalette(count) {
        const baseColors = [
            'rgba(54, 162, 235, 0.6)',   // Azul
            'rgba(255, 99, 132, 0.6)',   // Rojo
            'rgba(255, 206, 86, 0.6)',   // Amarillo
            'rgba(75, 192, 192, 0.6)',   // Verde
            'rgba(153, 102, 255, 0.6)',  // Púrpura
            'rgba(255, 159, 64, 0.6)',   // Naranja
            'rgba(199, 199, 199, 0.6)',  // Gris
            'rgba(83, 102, 255, 0.6)',   // Azul claro
            'rgba(255, 99, 255, 0.6)',   // Rosa
            'rgba(165, 42, 42, 0.6)',    // Marrón
            'rgba(0, 128, 128, 0.6)',    // Verde azulado
            'rgba(128, 0, 128, 0.6)',    // Púrpura oscuro
            'rgba(255, 215, 0, 0.6)',    // Dorado
            'rgba(192, 192, 192, 0.6)',  // Plata
            'rgba(139, 69, 19, 0.6)',    // Marrón oscuro
            'rgba(46, 139, 87, 0.6)'     // Verde mar
        ];
        
        // Si hay más elementos que colores base, generar colores aleatorios adicionales
        const colors = [];
        for (let i = 0; i < count; i++) {
            if (i < baseColors.length) {
                colors.push(baseColors[i]);
            } else {
                // Generar color aleatorio
                const r = Math.floor(Math.random() * 255);
                const g = Math.floor(Math.random() * 255);
                const b = Math.floor(Math.random() * 255);
                colors.push(`rgba(${r}, ${g}, ${b}, 0.6)`);
            }
        }
        
        return colors;
    }
</script>

<!-- Script para gráfica de Trampas con Mayor Captura -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const trampasMayorCaptura = <?= json_encode($trampasMayorCaptura ?? []); ?>;
        const plagaSeleccionada = "<?= $plagaSeleccionada ?? 'No seleccionada'; ?>";
        
        // Verificar si hay datos
        if (!trampasMayorCaptura || trampasMayorCaptura.length === 0) {
            console.log('No hay datos de trampas con mayor captura para la plaga: ' + plagaSeleccionada);
            return;
        }
        
        // Ordenar las trampas por total de capturas (de mayor a menor)
        trampasMayorCaptura.sort((a, b) => parseInt(b.total_capturas) - parseInt(a.total_capturas));
        
        // Preparar datos para el gráfico
        const labels = trampasMayorCaptura.map(item => item.trampa_nombre);
        const data = trampasMayorCaptura.map(item => parseInt(item.total_capturas) || 0);
        
        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('trampasMayorCapturaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'trampasMayorCapturaChart'");
            return;
        }
        
        // Generar un color principal para las barras (azul)
        const barColor = 'rgba(54, 162, 235, 0.8)';
        const borderColor = 'rgba(54, 162, 235, 1)';
        
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Cantidad de Capturas',
                    data: data,
                    backgroundColor: barColor,
                    borderColor: borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 10,
                        top: 20,
                        bottom: 20
                    }
                },
                plugins: {
                    legend: { 
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'TRAMPAS QUE PRESENTAN MAYOR CAPTURA DE ' + plagaSeleccionada.toUpperCase(),
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        title: { 
                            display: false
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 90,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: { 
                        beginAtZero: true, 
                        title: { 
                            display: false
                        },
                        grid: {
                            borderDash: [2, 2]
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
    });
</script>

<!-- Script para gráfica de Áreas que Presentaron Capturas -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const areasCapturasPorPlaga = <?= json_encode($areasCapturasPorPlaga ?? []); ?>;
        
        // Verificar si hay datos
        if (!areasCapturasPorPlaga || areasCapturasPorPlaga.length === 0) {
            console.log('No hay datos de áreas con capturas por plaga');
            return;
        }
        
        // Procesar datos para el gráfico de barras apiladas
        const ubicaciones = [...new Set(areasCapturasPorPlaga.map(item => item.ubicacion))];
        const plagas = [...new Set(areasCapturasPorPlaga.map(item => item.tipo_plaga))];
        
        // Crear datasets para cada tipo de plaga
        const datasets = [];
        const colores = generateColorPalette(plagas.length);
        
        plagas.forEach((plaga, index) => {
            const datosPorUbicacion = [];
            
            // Para cada ubicación, buscar la cantidad de capturas para esta plaga
            ubicaciones.forEach(ubicacion => {
                const item = areasCapturasPorPlaga.find(item => 
                    item.ubicacion === ubicacion && 
                    item.tipo_plaga === plaga
                );
                datosPorUbicacion.push(item ? parseInt(item.total_capturas) : 0);
            });
            
            datasets.push({
                label: plaga,
                data: datosPorUbicacion,
                backgroundColor: colores[index],
                borderColor: colores[index].replace('0.6', '1'),
                borderWidth: 1,
                borderRadius: 4,
                hoverOffset: 4,
                hoverBorderWidth: 2
            });
        });
        
        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('areasCapturasPorPlagaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'areasCapturasPorPlagaChart'");
            return;
        }
        
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ubicaciones,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 15,
                        right: 25,
                        top: 25,
                        bottom: 15
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            boxWidth: 12,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tipos de Plagas',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'ÁREAS QUE PRESENTARON CAPTURAS',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 0,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Ubicación',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            padding: {top: 10, bottom: 0}
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 10,
                                weight: 'bold'
                            },
                            padding: 5,
                            color: '#6b7280'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Capturas',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            },
                            padding: {top: 0, bottom: 10}
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11
                            },
                            padding: 8,
                            color: '#6b7280'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    });
</script>

<?= $this->endSection() ?> 