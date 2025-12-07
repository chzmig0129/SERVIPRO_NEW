<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .tab-header {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .tab-button {
        padding: 10px 20px;
        background-color: #e5e7eb;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s;
    }
    
    .tab-button.active {
        background-color: #3b82f6;
        color: white;
    }
    
    .tab-button:hover {
        background-color: #d1d5db;
    }
    
    .tab-button.active:hover {
        background-color: #2563eb;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .incidencias-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .incidencias-table th {
        background-color: #4472C4;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #345B8E;
        white-space: nowrap;
    }
    
    .incidencias-table td {
        padding: 10px;
        border: 1px solid #d1d5db;
        text-align: center;
    }
    
    .incidencias-table td:first-child,
    .incidencias-table td:nth-child(2) {
        text-align: left;
        background-color: #f9fafb;
        font-weight: 500;
    }
    
    .incidencias-table input[type="number"] {
        width: 100%;
        padding: 8px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        text-align: center;
        font-size: 14px;
    }
    
    .incidencias-table input[type="number"]:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .incidencias-table .total-cell {
        background-color: #f3f4f6;
        font-weight: bold;
        font-size: 14px;
    }
    
    .table-header {
        background-color: #f9fafb;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    
    .table-header h2 {
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 8px;
    }
    
    .table-header .date-info {
        color: #6b7280;
        font-size: 14px;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    .incidencias-agregadas {
        margin-top: 40px;
    }
    
    .incidencias-agregadas h3 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        color: #1f2937;
    }
    
    .incidencias-table-preview {
        width: 100%;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .incidencias-table-preview th {
        background-color: #64748b;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #475569;
        font-size: 13px;
    }
    
    .incidencias-table-preview td {
        padding: 10px;
        border: 1px solid #d1d5db;
        font-size: 13px;
    }
    
    .incidencias-table-preview input,
    .incidencias-table-preview select,
    .incidencias-table-preview textarea {
        width: 100%;
        padding: 6px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 13px;
    }
    
    .incidencias-table-preview .btn-eliminar {
        padding: 6px 12px;
        background-color: #ef4444;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .incidencias-table-preview .btn-eliminar:hover {
        background-color: #dc2626;
    }
    
    .no-incidencias {
        text-align: center;
        padding: 40px;
        color: #6b7280;
        font-style: italic;
    }
    
    .btn-guardar-todas {
        padding: 12px 24px;
        background-color: #10b981;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
        font-weight: 600;
        margin-top: 20px;
    }
    
    .btn-guardar-todas:hover {
        background-color: #059669;
    }
    
    .btn-guardar-todas:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }
    
    .inspector-global {
        padding: 15px;
        background-color: #f3f4f6;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    
    .inspector-global-content {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .inspector-global input[type="text"] {
        flex: 1;
        min-width: 200px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .inspector-global input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }
    
    .inspector-global label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #6b7280;
        cursor: pointer;
    }
    
    .inspector-global-legend {
        font-size: 12px;
        color: #6b7280;
        font-style: italic;
    }
    
    .incidencias-table-preview input:disabled,
    .incidencias-table-preview input[disabled] {
        background-color: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
        opacity: 0.7;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Subida de incidencias por Excel</h1>
            <p class="text-gray-500"><?= esc($plano['nombre']) ?> - <?= esc($sede['nombre']) ?></p>
        </div>
        <div class="flex gap-3">
            <button id="btnConfigurarPlantilla" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300">
                <i class="fas fa-cog"></i>
                Configurar Plantilla
            </button>
            <button id="btnDescargarPlantilla" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300">
                <i class="fas fa-download"></i>
                Descargar Excel
            </button>
            <button id="btnCargarExcel" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-300">
                <i class="fas fa-upload"></i>
                Cargar Excel
            </button>
            <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i>
                Volver al plano
            </a>
        </div>
    </div>

    <!-- Contenedor para las tablas HTML -->
    <div id="plantillaContainer" class="bg-white rounded-lg shadow-md p-6 hidden">
        <div id="tabsContainer" class="tab-header"></div>
        <div id="tablesContainer"></div>
    </div>

    <!-- Mensaje inicial -->
    <div id="mensajeInicial" class="bg-white rounded-lg shadow-md p-6">
        <div class="text-center py-8">
            <i class="fas fa-file-excel text-6xl text-gray-400 mb-4"></i>
            <h2 class="text-2xl font-bold text-gray-700 mb-2">Configura tu Plantilla</h2>
            <p class="text-gray-600 mb-6">Configura el número de semanas y la fecha de inicio para generar las tablas editables.</p>
            <button id="btnIniciarConfig" class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300">
                <i class="fas fa-cog"></i>
                Configurar Plantilla
            </button>
        </div>
    </div>
</div>

<!-- Modal para configurar plantilla -->
<div id="modalConfigurarPlantilla" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Configurar Plantilla</h3>
            <button id="cerrarModalConfig" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formConfigurarPlantilla">
            <div class="space-y-4">
                <div>
                    <label for="fecha_inicio_config" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Inicio
                    </label>
                    <input 
                        type="date" 
                        id="fecha_inicio_config" 
                        name="fecha_inicio" 
                        value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                
                <div>
                    <label for="semanas_config" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Semanas
                    </label>
                    <input 
                        type="number" 
                        id="semanas_config" 
                        name="semanas" 
                        value="4"
                        min="1"
                        max="52"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Se generará una tabla por cada semana (máximo 52 semanas)</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    id="cancelarConfig"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-check mr-2"></i>
                    Generar Tablas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para configurar descarga de plantilla (Excel) -->
<div id="modalDescargarPlantilla" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Descargar Excel</h3>
            <button id="cerrarModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formDescargarPlantilla" action="<?= base_url('blueprints-excel/descargarPlantilla/' . $plano['id']) ?>" method="GET">
            <div class="space-y-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Inicio
                    </label>
                    <input 
                        type="date" 
                        id="fecha_inicio" 
                        name="fecha_inicio" 
                        value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                </div>
                
                <div>
                    <label for="semanas" class="block text-sm font-medium text-gray-700 mb-2">
                        Número de Semanas
                    </label>
                    <input 
                        type="number" 
                        id="semanas" 
                        name="semanas" 
                        value="4"
                        min="1"
                        max="52"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Se generará una hoja por cada semana (máximo 52 semanas)</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    id="cancelarDescarga"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                >
                    <i class="fas fa-download mr-2"></i>
                    Descargar Plantilla
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para cargar Excel -->
<div id="modalCargarExcel" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-gray-800">Cargar Archivo Excel</h3>
            <button id="cerrarModalCargarExcel" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="formCargarExcel" enctype="multipart/form-data">
            <div class="space-y-4">
                <div>
                    <label for="archivo_excel" class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar archivo Excel
                    </label>
                    <input 
                        type="file" 
                        id="archivo_excel" 
                        name="archivo_excel" 
                        accept=".xlsx,.xls"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Solo archivos Excel (.xlsx, .xls)</p>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button 
                    type="button" 
                    id="cancelarCargarExcel"
                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                >
                    Cancelar
                </button>
                <button 
                    type="submit"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                >
                    <i class="fas fa-upload mr-2"></i>
                    Procesar Archivo
                </button>
            </div>
        </form>
        
        <div id="cargandoExcel" class="hidden mt-4 text-center">
            <i class="fas fa-spinner fa-spin text-purple-600 text-2xl mb-2"></i>
            <p class="text-gray-600">Procesando archivo Excel...</p>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Datos del plano y trampas desde PHP
const planoData = {
    id: <?= $plano['id'] ?>,
    nombre: <?= json_encode($plano['nombre']) ?>,
    sede: <?= json_encode($sede['nombre']) ?>
};

const trampasData = <?= json_encode($trampas ?? []) ?>;

// Obtener nombre del inspector de la sesión
const inspectorPorDefecto = <?= json_encode(session()->get('nombre') ?? 'Sistema') ?>;

// Tipos de insectos/plagas (basado en el modal de registrar incidencia)
const tiposInsectos = [
    'Mosca',
    'Mosca doméstica',
    'Mosca de la fruta',
    'Mosca de drenaje',
    'Moscas metálicas',
    'Mosca forida',
    'Palomillas de almacén',
    'Otras palomillas',
    'Gorgojos',
    'Otros escarabajos',
    'Abejas',
    'Avispas',
    'Mosquitos',
    'Cucaracha',
    'Hormiga',
    'Roedor',
    'Arañas',
    'Lagartijas',
    'Otros'
];

// Mapeo de tipos de insectos a tipos de plaga y tipo de insecto predeterminado
const mapeoTiposInsectos = {
    'Mosca': { tipoPlaga: 'mosca', tipoInsecto: 'Volador' },
    'Mosca doméstica': { tipoPlaga: 'mosca_domestica', tipoInsecto: 'Volador' },
    'Mosca de la fruta': { tipoPlaga: 'mosca_fruta', tipoInsecto: 'Volador' },
    'Mosca de drenaje': { tipoPlaga: 'mosca_drenaje', tipoInsecto: 'Volador' },
    'Moscas metálicas': { tipoPlaga: 'mosca_metalica', tipoInsecto: 'Volador' },
    'Mosca forida': { tipoPlaga: 'mosca_forida', tipoInsecto: 'Volador' },
    'Palomillas de almacén': { tipoPlaga: 'palomilla_almacen', tipoInsecto: 'Volador' },
    'Otras palomillas': { tipoPlaga: 'otras_palomillas', tipoInsecto: 'Volador' },
    'Gorgojos': { tipoPlaga: 'gorgojo', tipoInsecto: 'Rastrero' },
    'Otros escarabajos': { tipoPlaga: 'otros_escarabajos', tipoInsecto: 'Rastrero' },
    'Abejas': { tipoPlaga: 'abeja', tipoInsecto: 'Volador' },
    'Avispas': { tipoPlaga: 'avispa', tipoInsecto: 'Volador' },
    'Mosquitos': { tipoPlaga: 'mosquito', tipoInsecto: 'Volador' },
    'Cucaracha': { tipoPlaga: 'cucaracha', tipoInsecto: 'Rastrero' },
    'Hormiga': { tipoPlaga: 'hormiga', tipoInsecto: 'Rastrero' },
    'Roedor': { tipoPlaga: 'roedor', tipoInsecto: 'Rastrero' },
    'Arañas': { tipoPlaga: 'Arañas', tipoInsecto: 'Rastrero' },
    'Lagartijas': { tipoPlaga: 'Lagartija', tipoInsecto: 'Rastrero' },
    'Otros': { tipoPlaga: 'otro', tipoInsecto: 'Volador' }
};

// Mapeo inverso: de tipo_plaga a tipo_insecto predeterminado (para cuando se cambia el tipo de plaga en la tabla de incidencias)
const mapeoTipoPlagaATipoInsecto = {
    'mosca': 'Volador',
    'mosca_domestica': 'Volador',
    'mosca_fruta': 'Volador',
    'mosca_drenaje': 'Volador',
    'mosca_metalica': 'Volador',
    'mosca_forida': 'Volador',
    'palomilla_almacen': 'Volador',
    'otras_palomillas': 'Volador',
    'gorgojo': 'Rastrero',
    'otros_escarabajos': 'Rastrero',
    'abeja': 'Volador',
    'avispa': 'Volador',
    'mosquito': 'Volador',
    'cucaracha': 'Rastrero',
    'hormiga': 'Rastrero',
    'roedor': 'Rastrero',
    'Arañas': 'Rastrero',
    'Lagartija': 'Rastrero',
    'otro': 'Volador'
};

let semanasConfiguradas = [];
let fechaInicioConfig = null;
let incidenciasAgregadas = {}; // Objeto para almacenar incidencias por semana
let estadosInspectorGlobal = {}; // Objeto para almacenar el estado del checkbox y valor del inspector global por semana

    document.addEventListener('DOMContentLoaded', function() {
    const btnConfigurar = document.getElementById('btnConfigurarPlantilla');
    const btnIniciarConfig = document.getElementById('btnIniciarConfig');
    const btnDescargar = document.getElementById('btnDescargarPlantilla');
    const btnCargarExcel = document.getElementById('btnCargarExcel');
    const modalConfig = document.getElementById('modalConfigurarPlantilla');
    const modalDescarga = document.getElementById('modalDescargarPlantilla');
    const modalCargarExcel = document.getElementById('modalCargarExcel');
    const formConfig = document.getElementById('formConfigurarPlantilla');
    const formDescargar = document.getElementById('formDescargarPlantilla');
    const formCargarExcel = document.getElementById('formCargarExcel');
    const plantillaContainer = document.getElementById('plantillaContainer');
    const mensajeInicial = document.getElementById('mensajeInicial');

    // Abrir modal de configuración
    function abrirModalConfig() {
        modalConfig.classList.remove('hidden');
    }

    btnConfigurar.addEventListener('click', abrirModalConfig);
    btnIniciarConfig.addEventListener('click', abrirModalConfig);

    // Cerrar modales
    function cerrarModalConfig() {
        modalConfig.classList.add('hidden');
    }

    function cerrarModalDescarga() {
        modalDescarga.classList.add('hidden');
    }

    document.getElementById('cerrarModalConfig').addEventListener('click', cerrarModalConfig);
    document.getElementById('cancelarConfig').addEventListener('click', cerrarModalConfig);
    document.getElementById('cerrarModal').addEventListener('click', cerrarModalDescarga);
    document.getElementById('cancelarDescarga').addEventListener('click', cerrarModalDescarga);

    // Abrir modal de descarga
    btnDescargar.addEventListener('click', function() {
        modalDescarga.classList.remove('hidden');
    });

    // Abrir modal de cargar Excel
    if (btnCargarExcel) {
        btnCargarExcel.addEventListener('click', function() {
            modalCargarExcel.classList.remove('hidden');
        });
    }

    // Cerrar modal de cargar Excel
    function cerrarModalCargarExcel() {
        modalCargarExcel.classList.add('hidden');
        document.getElementById('cargandoExcel').classList.add('hidden');
        formCargarExcel.reset();
    }

    if (document.getElementById('cerrarModalCargarExcel')) {
        document.getElementById('cerrarModalCargarExcel').addEventListener('click', cerrarModalCargarExcel);
    }
    
    if (document.getElementById('cancelarCargarExcel')) {
        document.getElementById('cancelarCargarExcel').addEventListener('click', cerrarModalCargarExcel);
    }

    // Cerrar modales al hacer clic fuera
    modalConfig.addEventListener('click', function(e) {
        if (e.target === modalConfig) cerrarModalConfig();
    });

    modalDescarga.addEventListener('click', function(e) {
        if (e.target === modalDescarga) cerrarModalDescarga();
    });

    if (modalCargarExcel) {
        modalCargarExcel.addEventListener('click', function(e) {
            if (e.target === modalCargarExcel) cerrarModalCargarExcel();
        });
    }

    // Procesar carga de Excel
    if (formCargarExcel) {
        formCargarExcel.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const archivo = document.getElementById('archivo_excel').files[0];
            if (!archivo) {
                alert('Por favor seleccione un archivo Excel');
                return;
            }

            const formData = new FormData();
            formData.append('archivo_excel', archivo);

            // Mostrar indicador de carga
            document.getElementById('cargandoExcel').classList.remove('hidden');

            // Enviar archivo al servidor
            fetch('<?= base_url('blueprints-excel/procesarExcel/' . $plano['id']) ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('cargandoExcel').classList.add('hidden');
                
                if (data.success) {
                    // Procesar las incidencias recibidas
                    procesarIncidenciasDesdeExcel(data.incidencias);
                    cerrarModalCargarExcel();
                    
                    // Mostrar advertencia si algunas hojas no se procesaron
                    if (data.hojas_con_error > 0 && data.hojas_sin_formato && data.hojas_sin_formato.length > 0) {
                        let advertencia = `⚠️ ADVERTENCIA\n\n`;
                        advertencia += `Se procesaron ${data.hojas_procesadas} hoja(s) correctamente, pero ${data.hojas_con_error} hoja(s) no pudieron ser procesadas debido a formato incorrecto:\n\n`;
                        data.hojas_sin_formato.forEach(hoja => {
                            advertencia += `• ${hoja}\n`;
                        });
                        advertencia += `\nPor favor, revise el formato de estas hojas y descargue la plantilla si es necesario.`;
                        alert(advertencia);
                    }
                } else {
                    // Verificar el tipo de error
                    if (data.error_type === 'formato_incorrecto') {
                        // Crear un mensaje más detallado y claro
                        let mensaje = '⚠️ FORMATO DEL ARCHIVO INCORRECTO\n\n';
                        mensaje += 'No se pudo detectar el formato correcto del archivo Excel cargado.\n\n';
                        mensaje += 'El sistema no encontró los encabezados esperados ("ÁREA" y "EQUIPO") o las columnas de tipos de insectos.\n\n';
                        mensaje += 'SOLUCIÓN:\n';
                        mensaje += '1. Descargue la plantilla oficial usando el botón "Descargar Excel" de esta página\n';
                        mensaje += '2. Complete la plantilla descargada con sus datos\n';
                        mensaje += '3. Vuelva a cargar el archivo completado\n\n';
                        
                        if (data.hojas_sin_formato && Array.isArray(data.hojas_sin_formato) && data.hojas_sin_formato.length > 0) {
                            mensaje += 'Hojas que no se pudieron procesar:\n';
                            data.hojas_sin_formato.forEach(hoja => {
                                mensaje += '• ' + hoja + '\n';
                            });
                            mensaje += '\n';
                        }
                        
                        mensaje += '¿Desea descargar la plantilla ahora?';
                        
                        if (confirm(mensaje)) {
                            // Cerrar el modal de carga
                            cerrarModalCargarExcel();
                            // Abrir el modal de descarga automáticamente
                            setTimeout(() => {
                                modalDescarga.classList.remove('hidden');
                            }, 300);
                        }
                    } else if (data.error_type === 'archivo_invalido') {
                        // Error de archivo inválido o corrupto
                        let mensaje = '⚠️ ARCHIVO EXCEL INVÁLIDO O CORRUPTO\n\n';
                        mensaje += data.message || 'El archivo Excel no pudo ser procesado.\n\n';
                        mensaje += 'SOLUCIÓN:\n';
                        mensaje += '1. Verifique que el archivo no esté dañado o corrupto\n';
                        mensaje += '2. Descargue la plantilla oficial usando el botón "Descargar Excel" de esta página\n';
                        mensaje += '3. Complete la plantilla descargada con sus datos\n';
                        mensaje += '4. Vuelva a cargar el archivo completado\n\n';
                        mensaje += '¿Desea descargar la plantilla ahora?';
                        
                        if (confirm(mensaje)) {
                            // Cerrar el modal de carga
                            cerrarModalCargarExcel();
                            // Abrir el modal de descarga automáticamente
                            setTimeout(() => {
                                modalDescarga.classList.remove('hidden');
                            }, 300);
                        }
                    } else {
                        // Error general
                        alert('Error al procesar el archivo: ' + (data.message || 'Error desconocido'));
                    }
                }
            })
            .catch(error => {
                document.getElementById('cargandoExcel').classList.add('hidden');
                console.error('Error:', error);
                alert('Error al procesar el archivo. Por favor, intente nuevamente.');
            });
        });
    }

    // Generar tablas HTML
    formConfig.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const fechaInicio = document.getElementById('fecha_inicio_config').value;
        const semanas = parseInt(document.getElementById('semanas_config').value);

        if (semanas < 1 || semanas > 52) {
            alert('El número de semanas debe estar entre 1 y 52');
            return;
        }

        fechaInicioConfig = fechaInicio;
        incidenciasAgregadas = {}; // Limpiar incidencias previas
        generarTablasHTML(fechaInicio, semanas);
        cerrarModalConfig();
    });

    function generarTablasHTML(fechaInicio, numSemanas) {
        // Ocultar mensaje inicial y mostrar contenedor
        mensajeInicial.classList.add('hidden');
        plantillaContainer.classList.remove('hidden');

        // Limpiar contenedores
        document.getElementById('tabsContainer').innerHTML = '';
        document.getElementById('tablesContainer').innerHTML = '';

        semanasConfiguradas = [];
        const tabsContainer = document.getElementById('tabsContainer');
        const tablesContainer = document.getElementById('tablesContainer');

        // Crear tabs y tablas para cada semana
        for (let semana = 1; semana <= numSemanas; semana++) {
            // Crear fecha en hora local para evitar problemas de zona horaria
            const partesFecha = fechaInicio.split('-');
            const año = parseInt(partesFecha[0]);
            const mes = parseInt(partesFecha[1]) - 1; // Los meses en JS son 0-11
            const dia = parseInt(partesFecha[2]);
            
            const fechaSemana = new Date(año, mes, dia);
            fechaSemana.setDate(fechaSemana.getDate() + ((semana - 1) * 7));
            
            // Guardar fecha en formato YYYY-MM-DD
            const fechaFormatoISO = `${fechaSemana.getFullYear()}-${String(fechaSemana.getMonth() + 1).padStart(2, '0')}-${String(fechaSemana.getDate()).padStart(2, '0')}`;
            
            semanasConfiguradas.push({
                numero: semana,
                fechaInicio: fechaFormatoISO
            });

            // Crear tab
            const tab = document.createElement('button');
            tab.className = `tab-button ${semana === 1 ? 'active' : ''}`;
            tab.textContent = `Semana ${semana}`;
            tab.dataset.semana = semana;
            tab.addEventListener('click', () => mostrarSemana(semana));
            tabsContainer.appendChild(tab);

            // Crear contenedor para la tabla
            const tableDiv = document.createElement('div');
            tableDiv.id = `tabla-semana-${semana}`;
            tableDiv.className = `tab-content ${semana === 1 ? 'active' : ''}`;
            tableDiv.innerHTML = generarTablaHTML(semana, fechaSemana);
            tablesContainer.appendChild(tableDiv);
        }
    }

    function generarTablaHTML(semana, fecha) {
        const meses = ['ENERO', 'FEBRERO', 'MARZO', 'ABRIL', 'MAYO', 'JUNIO', 
                      'JULIO', 'AGOSTO', 'SEPTIEMBRE', 'OCTUBRE', 'NOVIEMBRE', 'DICIEMBRE'];
        const nombreMes = meses[fecha.getMonth()];
        const dia = String(fecha.getDate()).padStart(2, '0');
        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
        const año = fecha.getFullYear();
        const fechaFormateada = `${dia}/${mes}/${año}`;
        
        const titulo = `REGISTRO DE ACTIVIDAD DE INSECTOS VOLADORES EN ${planoData.sede.toUpperCase()} ${planoData.nombre.toUpperCase()} ${año}`;
        
        let html = `
            <div class="table-header">
                <h1 style="font-size: 18px; font-weight: bold; margin-bottom: 10px;">SERVIPRO</h1>
                <h2 style="font-size: 14px; font-weight: bold; margin-bottom: 8px;">${titulo}</h2>
                <div class="date-info">
                    <strong>${nombreMes}</strong><br>
                    Fecha en la que se registrarán las incidencias: <strong>${fechaFormateada}</strong>
                </div>
            </div>
            
            <div class="table-container">
                <table class="incidencias-table">
                    <thead>
                        <tr>
                            <th>ÁREA</th>
                            <th>EQUIPO</th>
        `;
        
        // Agregar encabezados de tipos de insectos
        tiposInsectos.forEach(tipo => {
            html += `<th>${tipo}</th>`;
        });
        
        html += `
                            <th>Total de insectos</th>
                        </tr>
                    </thead>
                    <tbody>
        `;
        
        // Agregar filas de trampas
        trampasData.forEach((trampa, index) => {
            const idTrampa = trampa.id_trampa || trampa.nombre || 'T' + trampa.id;
            const ubicacion = trampa.ubicacion || 'Sin ubicación';
            const rowId = `semana-${semana}-trampa-${trampa.id}`;
            
            html += `
                        <tr>
                            <td>${ubicacion}</td>
                            <td>${idTrampa}</td>
            `;
            
            // Agregar inputs para cada tipo de insecto
            tiposInsectos.forEach((tipo, colIndex) => {
                const inputId = `${rowId}-insecto-${colIndex}`;
                html += `
                            <td>
                                <input 
                                    type="number" 
                                    id="${inputId}"
                                    class="insecto-input"
                                    data-semana="${semana}"
                                    data-trampa-id="${trampa.id}"
                                    data-tipo-index="${colIndex}"
                                    data-row-index="${index}"
                                    value="0" 
                                    min="0"
                                    step="1"
                                >
                            </td>
                `;
            });
            
            // Celda de total (se calculará automáticamente)
            html += `
                            <td class="total-cell" id="${rowId}-total">0</td>
                        </tr>
            `;
        });
        
        html += `
                    </tbody>
                </table>
            </div>
            
            <!-- Tabla de incidencias agregadas -->
            <div class="incidencias-agregadas">
                <h3>Incidencias agregadas: <span id="contador-incidencias-semana-${semana}">0</span></h3>
                <div id="tabla-incidencias-semana-${semana}">
                    <div class="no-incidencias">No hay incidencias agregadas. Modifica los valores en la tabla para generar incidencias.</div>
                </div>
                <button class="btn-guardar-todas" id="btn-guardar-semana-${semana}" disabled>
                    <i class="fas fa-save mr-2"></i>
                    Guardar todas las incidencias
                </button>
            </div>
        `;
        
        return html;
    }

    function mostrarSemana(numSemana) {
        // Cambiar tabs activos
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.semana == numSemana);
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.toggle('active', content.id === `tabla-semana-${numSemana}`);
        });
    }

    // Calcular totales automáticamente cuando cambia un valor y generar incidencias
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('insecto-input')) {
            calcularTotalFila(e.target);
            actualizarIncidencias(e.target);
        }
    });

    function calcularTotalFila(input) {
        const semana = input.dataset.semana;
        const rowIndex = input.dataset.rowIndex;
        const trampaId = input.dataset.trampaId;
        const rowId = `semana-${semana}-trampa-${trampaId}`;
        
        // Obtener todos los inputs de la misma fila
        const inputs = document.querySelectorAll(`.insecto-input[data-semana="${semana}"][data-row-index="${rowIndex}"]`);
        
        // Calcular suma
        let total = 0;
        inputs.forEach(inp => {
            const valor = parseFloat(inp.value) || 0;
            total += valor;
        });
        
        // Actualizar celda de total
        const totalCell = document.getElementById(`${rowId}-total`);
        if (totalCell) {
            totalCell.textContent = total;
        }
    }

    function actualizarIncidencias(input) {
        const semana = input.dataset.semana;
        const trampaId = input.dataset.trampaId;
        const tipoIndex = parseInt(input.dataset.tipoIndex);
        const valor = parseFloat(input.value) || 0;
        
        // Obtener datos de la trampa
        const trampa = trampasData.find(t => t.id == trampaId);
        if (!trampa) return;
        
        const idTrampa = trampa.id_trampa || trampa.nombre || 'T' + trampa.id;
        const tipoInsecto = tiposInsectos[tipoIndex];
        const mapeo = mapeoTiposInsectos[tipoInsecto];
        
        if (!mapeo) return;
        
        // Obtener fecha de la semana
        const semanaConfig = semanasConfiguradas.find(s => s.numero == semana);
        if (!semanaConfig) return;
        
        const fechaIncidencia = semanaConfig.fechaInicio;
        
        // Inicializar array de incidencias para esta semana si no existe
        if (!incidenciasAgregadas[semana]) {
            incidenciasAgregadas[semana] = [];
        }
        
        // Generar ID único para esta incidencia
        const incidenciaId = `${semana}-${trampaId}-${tipoIndex}`;
        
        // Buscar si ya existe una incidencia con este ID
        const indexExistente = incidenciasAgregadas[semana].findIndex(inc => inc.id === incidenciaId);
        
        if (valor > 0) {
            // Verificar si hay inspector global activo
            let inspectorFinal = inspectorPorDefecto;
            const checkboxGlobal = document.getElementById(`checkbox-inspector-global-semana-${semana}`);
            const inputGlobal = document.getElementById(`inspector-global-semana-${semana}`);
            
            if (checkboxGlobal && checkboxGlobal.checked && inputGlobal) {
                const nombreGlobal = inputGlobal.value.trim();
                if (nombreGlobal) {
                    inspectorFinal = nombreGlobal;
                }
            }
            
            // Crear o actualizar incidencia
            const incidencia = {
                id: incidenciaId,
                id_trampa: idTrampa,
                trampa_id: trampaId,
                tipo_plaga: mapeo.tipoPlaga,
                tipo_insecto: mapeo.tipoInsecto,
                tipo_incidencia: 'Captura',
                cantidad_organismos: valor,
                fecha_incidencia: fechaIncidencia,
                inspector: inspectorFinal,
                notas: ''
            };
            
            if (indexExistente >= 0) {
                // Actualizar incidencia existente
                incidenciasAgregadas[semana][indexExistente] = incidencia;
            } else {
                // Agregar nueva incidencia
                incidenciasAgregadas[semana].push(incidencia);
            }
        } else {
            // Si el valor es 0, eliminar la incidencia si existe
            if (indexExistente >= 0) {
                incidenciasAgregadas[semana].splice(indexExistente, 1);
            }
        }
        
        // Actualizar la tabla de incidencias
        actualizarTablaIncidencias(semana);
    }

    function actualizarTablaIncidencias(semana) {
        const contenedor = document.getElementById(`tabla-incidencias-semana-${semana}`);
        const contador = document.getElementById(`contador-incidencias-semana-${semana}`);
        const btnGuardar = document.getElementById(`btn-guardar-semana-${semana}`);
        
        if (!contenedor) return;
        
        const incidencias = incidenciasAgregadas[semana] || [];
        
        // Actualizar contador
        if (contador) {
            contador.textContent = incidencias.length;
        }
        
        // Habilitar/deshabilitar botón de guardar
        if (btnGuardar) {
            btnGuardar.disabled = incidencias.length === 0;
        }
        
        if (incidencias.length === 0) {
            contenedor.innerHTML = '<div class="no-incidencias">No hay incidencias agregadas. Modifica los valores en la tabla para generar incidencias.</div>';
            return;
        }
        
        // Generar tabla de incidencias con campo de inspector global
        let html = `
            <div class="inspector-global">
                <div class="inspector-global-content">
                    <input type="text" 
                           id="inspector-global-semana-${semana}" 
                           class="inspector-global-input"
                           placeholder="Nombre del inspector"
                           data-semana="${semana}">
                    <label>
                        <input type="checkbox" 
                               id="checkbox-inspector-global-semana-${semana}"
                               class="checkbox-inspector-global"
                               data-semana="${semana}">
                        <span class="inspector-global-legend">Se aplicará en todas las columnas</span>
                    </label>
                </div>
            </div>
            
            <table class="incidencias-table-preview">
                <thead>
                    <tr>
                        <th>ID Trampa</th>
                        <th>Tipo de Plaga</th>
                        <th>Tipo de Incidencia</th>
                        <th>Tipo de Insecto</th>
                        <th>Cantidad</th>
                        <th>Fecha</th>
                        <th>Inspector</th>
                        <th>Notas</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
        `;
        
        incidencias.forEach((incidencia, index) => {
            html += `
                    <tr data-incidencia-id="${incidencia.id}">
                        <td>${incidencia.id_trampa}</td>
                        <td>
                            <select class="editable-tipo-plaga" data-incidencia-id="${incidencia.id}" data-semana="${semana}">
                                <option value="mosca" ${incidencia.tipo_plaga === 'mosca' ? 'selected' : ''}>Mosca</option>
                                <option value="mosca_domestica" ${incidencia.tipo_plaga === 'mosca_domestica' ? 'selected' : ''}>Mosca Doméstica</option>
                                <option value="mosca_fruta" ${incidencia.tipo_plaga === 'mosca_fruta' ? 'selected' : ''}>Mosca De La Fruta</option>
                                <option value="mosca_drenaje" ${incidencia.tipo_plaga === 'mosca_drenaje' ? 'selected' : ''}>Mosca De Drenaje</option>
                                <option value="mosca_metalica" ${incidencia.tipo_plaga === 'mosca_metalica' ? 'selected' : ''}>Moscas Metálicas</option>
                                <option value="mosca_forida" ${incidencia.tipo_plaga === 'mosca_forida' ? 'selected' : ''}>Mosca Forida</option>
                                <option value="palomilla_almacen" ${incidencia.tipo_plaga === 'palomilla_almacen' ? 'selected' : ''}>Palomillas De Almacén</option>
                                <option value="otras_palomillas" ${incidencia.tipo_plaga === 'otras_palomillas' ? 'selected' : ''}>Otras Palomillas</option>
                                <option value="gorgojo" ${incidencia.tipo_plaga === 'gorgojo' ? 'selected' : ''}>Gorgojos</option>
                                <option value="otros_escarabajos" ${incidencia.tipo_plaga === 'otros_escarabajos' ? 'selected' : ''}>Otros Escarabajos</option>
                                <option value="abeja" ${incidencia.tipo_plaga === 'abeja' ? 'selected' : ''}>Abejas</option>
                                <option value="avispa" ${incidencia.tipo_plaga === 'avispa' ? 'selected' : ''}>Avispas</option>
                                <option value="mosquito" ${incidencia.tipo_plaga === 'mosquito' ? 'selected' : ''}>Mosquitos</option>
                                <option value="cucaracha" ${incidencia.tipo_plaga === 'cucaracha' ? 'selected' : ''}>Cucaracha</option>
                                <option value="hormiga" ${incidencia.tipo_plaga === 'hormiga' ? 'selected' : ''}>Hormiga</option>
                                <option value="roedor" ${incidencia.tipo_plaga === 'roedor' ? 'selected' : ''}>Roedor</option>
                                <option value="Arañas" ${incidencia.tipo_plaga === 'Arañas' ? 'selected' : ''}>Arañas</option>
                                <option value="Lagartija" ${incidencia.tipo_plaga === 'Lagartija' ? 'selected' : ''}>Lagartijas</option>
                                <option value="otro" ${incidencia.tipo_plaga === 'otro' ? 'selected' : ''}>Otro</option>
                            </select>
                        </td>
                        <td>
                            <select class="editable-tipo-incidencia" data-incidencia-id="${incidencia.id}" data-semana="${semana}">
                                <option value="Captura" ${incidencia.tipo_incidencia === 'Captura' ? 'selected' : ''}>Captura</option>
                                <option value="Hallazgo" ${incidencia.tipo_incidencia === 'Hallazgo' ? 'selected' : ''}>Hallazgo</option>
                            </select>
                        </td>
                        <td>
                            <select class="editable-tipo-insecto" data-incidencia-id="${incidencia.id}" data-semana="${semana}">
                                <option value="Volador" ${incidencia.tipo_insecto === 'Volador' ? 'selected' : ''}>Volador</option>
                                <option value="Rastrero" ${incidencia.tipo_insecto === 'Rastrero' ? 'selected' : ''}>Rastrero</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="editable-cantidad" data-incidencia-id="${incidencia.id}" data-semana="${semana}" 
                                   value="${incidencia.cantidad_organismos}" min="1" step="1">
                        </td>
                        <td>${formatearFecha(incidencia.fecha_incidencia)}</td>
                        <td>
                            <input type="text" class="editable-inspector" data-incidencia-id="${incidencia.id}" data-semana="${semana}" 
                                   value="${incidencia.inspector}" placeholder="Nombre del inspector" id="inspector-${incidencia.id}">
                        </td>
                        <td>
                            <textarea class="editable-notas" data-incidencia-id="${incidencia.id}" data-semana="${semana}" 
                                      rows="2" placeholder="Notas adicionales">${incidencia.notas || ''}</textarea>
                        </td>
                        <td>
                            <button class="btn-eliminar" onclick="eliminarIncidencia('${incidencia.id}', ${semana})">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
            `;
        });
        
        html += `
                </tbody>
            </table>
        `;
        
        contenedor.innerHTML = html;
        
        // Agregar event listeners para campos editables
        agregarEventListenersEdicion(semana);
        
        // Agregar event listeners para inspector global
        agregarEventListenersInspectorGlobal(semana);
    }
    
    function agregarEventListenersInspectorGlobal(semana) {
        const inputGlobal = document.getElementById(`inspector-global-semana-${semana}`);
        const checkboxGlobal = document.getElementById(`checkbox-inspector-global-semana-${semana}`);
        
        if (!inputGlobal || !checkboxGlobal) return;
        
        // Función para aplicar inspector a todas las incidencias
        function aplicarInspectorGlobal() {
            if (!checkboxGlobal.checked) {
                // Si se desmarca, restaurar inspector por defecto
                incidenciasAgregadas[semana].forEach(incidencia => {
                    incidencia.inspector = inspectorPorDefecto;
                });
                // Actualizar todos los inputs individuales
                document.querySelectorAll(`.editable-inspector[data-semana="${semana}"]`).forEach(input => {
                    input.value = inspectorPorDefecto;
                });
            } else {
                // Si está marcado, aplicar el valor del input global
                const nombreInspector = inputGlobal.value.trim() || inspectorPorDefecto;
                incidenciasAgregadas[semana].forEach(incidencia => {
                    incidencia.inspector = nombreInspector;
                });
                // Actualizar todos los inputs individuales
                document.querySelectorAll(`.editable-inspector[data-semana="${semana}"]`).forEach(input => {
                    input.value = nombreInspector;
                });
            }
        }
        
        // Función para habilitar/deshabilitar inputs individuales
        function toggleInputsIndividuales(deshabilitar) {
            document.querySelectorAll(`.editable-inspector[data-semana="${semana}"]`).forEach(input => {
                input.disabled = deshabilitar;
            });
        }
        
        // Event listener para checkbox
        checkboxGlobal.addEventListener('change', function() {
            toggleInputsIndividuales(this.checked);
            aplicarInspectorGlobal();
        });
        
        // Event listener para input - solo aplica si el checkbox está marcado
        inputGlobal.addEventListener('input', function() {
            if (checkboxGlobal.checked) {
                const nombreInspector = this.value.trim() || inspectorPorDefecto;
                incidenciasAgregadas[semana].forEach(incidencia => {
                    incidencia.inspector = nombreInspector;
                });
                // Actualizar todos los inputs individuales
                document.querySelectorAll(`.editable-inspector[data-semana="${semana}"]`).forEach(input => {
                    input.value = nombreInspector;
                });
            }
        });
    }

    function agregarEventListenersEdicion(semana) {
        // Tipo de incidencia
        document.querySelectorAll(`.editable-tipo-incidencia[data-semana="${semana}"]`).forEach(select => {
            select.addEventListener('change', function() {
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    incidencia.tipo_incidencia = this.value;
                }
            });
        });
        
        // Tipo de plaga - actualizar automáticamente tipo_insecto cuando cambia
        document.querySelectorAll(`.editable-tipo-plaga[data-semana="${semana}"]`).forEach(select => {
            select.addEventListener('change', function() {
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    incidencia.tipo_plaga = this.value;
                    
                    // Actualizar automáticamente el tipo de insecto según el mapeo predeterminado
                    const tipoInsectoPredeterminado = mapeoTipoPlagaATipoInsecto[this.value];
                    if (tipoInsectoPredeterminado) {
                        incidencia.tipo_insecto = tipoInsectoPredeterminado;
                        
                        // Actualizar el select de tipo_insecto en la interfaz
                        const selectTipoInsecto = document.querySelector(`.editable-tipo-insecto[data-incidencia-id="${incidenciaId}"][data-semana="${semana}"]`);
                        if (selectTipoInsecto) {
                            selectTipoInsecto.value = tipoInsectoPredeterminado;
                        }
                    }
                }
            });
        });
        
        // Tipo de insecto
        document.querySelectorAll(`.editable-tipo-insecto[data-semana="${semana}"]`).forEach(select => {
            select.addEventListener('change', function() {
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    incidencia.tipo_insecto = this.value;
                }
            });
        });
        
        // Cantidad
        document.querySelectorAll(`.editable-cantidad[data-semana="${semana}"]`).forEach(input => {
            input.addEventListener('change', function() {
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    const nuevaCantidad = parseFloat(this.value) || 1;
                    incidencia.cantidad_organismos = nuevaCantidad;
                    this.value = nuevaCantidad;
                }
            });
        });
        
        // Inspector - solo actualizar si el checkbox global no está marcado
        document.querySelectorAll(`.editable-inspector[data-semana="${semana}"]`).forEach(input => {
            input.addEventListener('change', function() {
                const checkboxGlobal = document.getElementById(`checkbox-inspector-global-semana-${semana}`);
                
                // Si el checkbox global está marcado, no permitir cambios individuales
                // (el cambio global se maneja en agregarEventListenersInspectorGlobal)
                if (checkboxGlobal && checkboxGlobal.checked) {
                    return;
                }
                
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    incidencia.inspector = this.value || inspectorPorDefecto;
                }
            });
        });
        
        // Notas
        document.querySelectorAll(`.editable-notas[data-semana="${semana}"]`).forEach(textarea => {
            textarea.addEventListener('change', function() {
                const incidenciaId = this.dataset.incidenciaId;
                const incidencia = incidenciasAgregadas[semana].find(inc => inc.id === incidenciaId);
                if (incidencia) {
                    incidencia.notas = this.value || '';
                }
            });
        });
    }

    function eliminarIncidencia(incidenciaId, semana) {
        if (!incidenciasAgregadas[semana]) return;
        
        const index = incidenciasAgregadas[semana].findIndex(inc => inc.id === incidenciaId);
        if (index >= 0) {
            const incidencia = incidenciasAgregadas[semana][index];
            
            // Resetear el valor en la tabla principal a 0
            const trampaId = incidencia.trampa_id;
            const tipoIndex = tiposInsectos.findIndex(tipo => {
                const mapeo = mapeoTiposInsectos[tipo];
                return mapeo && mapeo.tipoPlaga === incidencia.tipo_plaga;
            });
            
            if (tipoIndex >= 0) {
                const trampaIndex = trampasData.findIndex(t => t.id == trampaId);
                if (trampaIndex >= 0) {
                    const inputId = `semana-${semana}-trampa-${trampaId}-insecto-${tipoIndex}`;
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.value = 0;
                        calcularTotalFila(input);
                    }
                }
            }
            
            incidenciasAgregadas[semana].splice(index, 1);
            actualizarTablaIncidencias(semana);
        }
    }

    function formatearFecha(fecha) {
        if (!fecha) return '';
        // Crear fecha en hora local para evitar problemas de zona horaria
        const partesFecha = fecha.split('-');
        if (partesFecha.length !== 3) return fecha;
        
        const año = parseInt(partesFecha[0]);
        const mes = parseInt(partesFecha[1]) - 1; // Los meses en JS son 0-11
        const dia = parseInt(partesFecha[2]);
        
        const diaFormateado = String(dia).padStart(2, '0');
        const mesFormateado = String(mes + 1).padStart(2, '0');
        
        return `${diaFormateado}/${mesFormateado}/${año}`;
    }

    // Función global para eliminar incidencias (necesaria para los botones)
    window.eliminarIncidencia = eliminarIncidencia;

    // Agregar event listeners para los botones de guardar todas las incidencias
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id && e.target.id.startsWith('btn-guardar-semana-')) {
            const semana = parseInt(e.target.id.replace('btn-guardar-semana-', ''));
            guardarTodasIncidencias(semana);
        }
    });

    function guardarTodasIncidencias(semana) {
        const incidencias = incidenciasAgregadas[semana] || [];
        
        if (incidencias.length === 0) {
            alert('No hay incidencias para guardar');
            return;
        }
        
        const btnGuardar = document.getElementById(`btn-guardar-semana-${semana}`);
        if (btnGuardar) {
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Guardando...';
        }
        
        let guardadas = 0;
        let errores = 0;
        const mensajesError = [];
        
        const promises = incidencias.map((incidencia, index) => {
            return new Promise((resolve) => {
                const formData = new FormData();
                formData.append('trampa_id', incidencia.trampa_id); // Usar el ID de la trampa
                formData.append('tipo_plaga', incidencia.tipo_plaga);
                formData.append('tipo_incidencia', incidencia.tipo_incidencia);
                formData.append('tipo_insecto', incidencia.tipo_insecto);
                formData.append('cantidad_organismos', incidencia.cantidad_organismos);
                formData.append('notas', incidencia.notas || '');
                formData.append('inspector', incidencia.inspector || inspectorPorDefecto);
                formData.append('fecha_incidencia', incidencia.fecha_incidencia + ' 00:00:00');
                
                fetch('<?= base_url('blueprints/guardar_incidencia') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        guardadas++;
                    } else {
                        errores++;
                        mensajesError.push(`Incidencia ${index + 1}: ${data.message || 'Error desconocido'}`);
                    }
                    resolve();
                })
                .catch(error => {
                    errores++;
                    mensajesError.push(`Incidencia ${index + 1}: Error de conexión`);
                    console.error('Error al guardar incidencia:', error);
                    resolve();
                });
            });
        });
        
        Promise.all(promises).then(() => {
            if (btnGuardar) {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = '<i class="fas fa-save mr-2"></i> Guardar todas las incidencias';
            }
            
            let mensaje = `${guardadas} incidencias guardadas correctamente`;
            if (errores > 0) {
                mensaje += `, ${errores} errores`;
                if (mensajesError.length > 0) {
                    mensaje += '\n' + mensajesError.slice(0, 3).join('\n');
                    if (mensajesError.length > 3) {
                        mensaje += `\n... y ${mensajesError.length - 3} más`;
                    }
                }
                alert(mensaje);
            } else {
                alert(mensaje);
                // Limpiar incidencias guardadas
                incidenciasAgregadas[semana] = [];
                actualizarTablaIncidencias(semana);
                
                // Resetear todos los inputs de la tabla a 0
                document.querySelectorAll(`.insecto-input[data-semana="${semana}"]`).forEach(input => {
                    input.value = 0;
                });
                
                // Recalcular totales
                document.querySelectorAll(`.insecto-input[data-semana="${semana}"]`).forEach(input => {
                    calcularTotalFila(input);
                });
            }
        });
    }

    // Función para procesar incidencias cargadas desde Excel
    function procesarIncidenciasDesdeExcel(incidenciasRecibidas) {
        // Asegurar que incidenciasRecibidas sea un array
        if (!incidenciasRecibidas) {
            alert('No se recibieron datos del archivo Excel');
            return;
        }
        
        // Convertir a array si no lo es
        if (!Array.isArray(incidenciasRecibidas)) {
            console.warn('incidenciasRecibidas no es un array, convirtiendo...', incidenciasRecibidas);
            // Si es un objeto, intentar extraer un array de él
            if (typeof incidenciasRecibidas === 'object' && incidenciasRecibidas !== null) {
                // Si tiene una propiedad 'incidencias' que sea un array no vacío, usarla
                if (incidenciasRecibidas.incidencias && Array.isArray(incidenciasRecibidas.incidencias) && incidenciasRecibidas.incidencias.length > 0) {
                    incidenciasRecibidas = incidenciasRecibidas.incidencias;
                } else {
                    // Extraer solo las propiedades numéricas (índices de array como 0, 1, 2, etc.)
                    // Estas son las incidencias reales cuando el objeto tiene estructura {0: {...}, 1: {...}, ...}
                    const claves = Object.keys(incidenciasRecibidas);
                    const clavesNumericas = claves.filter(k => /^\d+$/.test(k)); // Solo claves que son números
                    
                    if (clavesNumericas.length > 0) {
                        // Extraer las incidencias de las propiedades numéricas
                        incidenciasRecibidas = clavesNumericas
                            .map(k => incidenciasRecibidas[k])
                            .filter(item => 
                                item !== null && 
                                item !== undefined && 
                                typeof item === 'object' && 
                                !Array.isArray(item) &&
                                (item.id_trampa !== undefined || item.trampa_id !== undefined) // Verificar que sea una incidencia válida
                            );
                    } else {
                        // Si no hay claves numéricas, intentar Object.values pero filtrar propiedades especiales
                        const valores = Object.entries(incidenciasRecibidas)
                            .filter(([key]) => key !== 'incidencias' && key !== 'error')
                            .map(([, value]) => value)
                            .filter(item => 
                                item !== null && 
                                item !== undefined && 
                                typeof item === 'object' && 
                                !Array.isArray(item) &&
                                (item.id_trampa !== undefined || item.trampa_id !== undefined)
                            );
                        incidenciasRecibidas = valores;
                    }
                }
            } else {
                incidenciasRecibidas = [];
            }
        }
        
        // Filtrar cualquier valor null o undefined que pueda haber quedado
        incidenciasRecibidas = incidenciasRecibidas.filter(item => 
            item !== null && 
            item !== undefined && 
            typeof item === 'object' &&
            !Array.isArray(item)
        );
        
        if (incidenciasRecibidas.length === 0) {
            alert('No se encontraron incidencias en el archivo Excel');
            return;
        }

        // Ocultar mensaje inicial y mostrar contenedor
        mensajeInicial.classList.add('hidden');
        plantillaContainer.classList.remove('hidden');

        // Limpiar contenedores
        document.getElementById('tabsContainer').innerHTML = '';
        document.getElementById('tablesContainer').innerHTML = '';

        // Agrupar incidencias por semana
        const incidenciasPorSemana = {};
        const semanasSet = new Set();

        incidenciasRecibidas.forEach(incidencia => {
            // Validar que la incidencia sea válida
            if (!incidencia || typeof incidencia !== 'object') {
                console.warn('Incidencia inválida encontrada, omitiendo:', incidencia);
                return;
            }
            
            const semana = incidencia.semana || 1;
            semanasSet.add(semana);
            
            if (!incidenciasPorSemana[semana]) {
                incidenciasPorSemana[semana] = [];
            }
            
            // Generar ID único para la incidencia
            const incidenciaId = `excel-${semana}-${incidencia.trampa_id || 'unknown'}-${incidencia.tipo_plaga || 'unknown'}-${Date.now()}-${Math.random()}`;
            incidencia.id = incidenciaId;
            
            incidenciasPorSemana[semana].push(incidencia);
        });

        // Ordenar semanas
        const semanasOrdenadas = Array.from(semanasSet).sort((a, b) => a - b);
        
        // Obtener la fecha más temprana de todas las incidencias para configurar
        const fechas = incidenciasRecibidas
            .filter(inc => inc && typeof inc === 'object')
            .map(inc => inc.fecha_incidencia)
            .filter(f => f);
        const fechaMinima = fechas.length > 0 ? fechas.sort()[0] : new Date().toISOString().split('T')[0];

        semanasConfiguradas = [];
        incidenciasAgregadas = {};

        const tabsContainer = document.getElementById('tabsContainer');
        const tablesContainer = document.getElementById('tablesContainer');

        // Crear tabs y contenedores para cada semana
        semanasOrdenadas.forEach((semanaNum, index) => {
            // Obtener una fecha de ejemplo para esta semana
            const incidenciaEjemplo = incidenciasPorSemana[semanaNum][0];
            const fechaSemana = new Date(incidenciaEjemplo.fecha_incidencia + 'T00:00:00');
            
            semanasConfiguradas.push({
                numero: semanaNum,
                fechaInicio: incidenciaEjemplo.fecha_incidencia
            });

            // Crear tab
            const tab = document.createElement('button');
            tab.className = `tab-button ${index === 0 ? 'active' : ''}`;
            tab.textContent = `Semana ${semanaNum}`;
            tab.dataset.semana = semanaNum;
            tab.addEventListener('click', () => mostrarSemana(semanaNum));
            tabsContainer.appendChild(tab);

            // Crear contenedor para la tabla
            const tableDiv = document.createElement('div');
            tableDiv.id = `tabla-semana-${semanaNum}`;
            tableDiv.className = `tab-content ${index === 0 ? 'active' : ''}`;
            tableDiv.innerHTML = generarTablaHTML(semanaNum, fechaSemana);
            tablesContainer.appendChild(tableDiv);

            // Asignar las incidencias a esta semana
            incidenciasAgregadas[semanaNum] = incidenciasPorSemana[semanaNum];
        });

        // Actualizar todas las tablas de incidencias y los valores en la tabla principal
        // Usar setTimeout para asegurar que las tablas se hayan renderizado completamente
        setTimeout(() => {
            semanasOrdenadas.forEach(semanaNum => {
                actualizarTablaIncidencias(semanaNum);
                actualizarValoresTablaPrincipal(semanaNum, incidenciasPorSemana[semanaNum]);
                agregarEventListenersEdicion(semanaNum);
                agregarEventListenersInspectorGlobal(semanaNum);
            });
        }, 100);
    }

    // Función auxiliar para encontrar el índice del tipo de insecto basado en el tipo_plaga
    function encontrarIndicePorTipoPlaga(tipoPlaga) {
        // Normalizar el tipo_plaga (a minúsculas y sin espacios)
        const tipoPlagaNormalizado = tipoPlaga.toLowerCase().trim();
        
        // Buscar en el array de tiposInsectos
        for (let index = 0; index < tiposInsectos.length; index++) {
            const tipo = tiposInsectos[index];
            if (mapeoTiposInsectos[tipo]) {
                const tipoPlagaMapeo = mapeoTiposInsectos[tipo].tipoPlaga;
                const tipoPlagaMapeoNormalizado = tipoPlagaMapeo.toLowerCase().trim();
                
                // Comparación exacta o parcial
                if (tipoPlagaNormalizado === tipoPlagaMapeoNormalizado) {
                    return index;
                }
            }
        }
        
        return -1;
    }

    // Función para actualizar los valores en la tabla principal basándose en las incidencias cargadas
    function actualizarValoresTablaPrincipal(semana, incidencias) {
        if (!incidencias || incidencias.length === 0) {
            console.log('No hay incidencias para actualizar en la tabla principal');
            return;
        }

        console.log(`Actualizando valores de tabla principal para semana ${semana} con ${incidencias.length} incidencias`);

        // Agrupar incidencias por trampa_id y tipo_plaga para sumar cantidades si hay múltiples
        const incidenciasAgrupadas = {};
        incidencias.forEach(incidencia => {
            const key = `${incidencia.trampa_id}-${incidencia.tipo_plaga}`;
            if (!incidenciasAgrupadas[key]) {
                incidenciasAgrupadas[key] = {
                    trampa_id: incidencia.trampa_id,
                    tipo_plaga: incidencia.tipo_plaga,
                    cantidad: 0
                };
            }
            incidenciasAgrupadas[key].cantidad += parseInt(incidencia.cantidad_organismos) || 0;
        });

        console.log('Incidencias agrupadas:', incidenciasAgrupadas);

        // Actualizar los inputs en la tabla principal
        Object.values(incidenciasAgrupadas).forEach(incidenciaAgrupada => {
            const trampaId = incidenciaAgrupada.trampa_id;
            const tipoPlaga = incidenciaAgrupada.tipo_plaga;
            const cantidad = incidenciaAgrupada.cantidad;
            
            // Buscar el índice del tipo de insecto
            const tipoIndex = encontrarIndicePorTipoPlaga(tipoPlaga);

            if (tipoIndex < 0) {
                console.warn(`No se encontró índice para tipo_plaga: ${tipoPlaga}`);
                return;
            }

            // Buscar el input correspondiente
            const inputId = `semana-${semana}-trampa-${trampaId}-insecto-${tipoIndex}`;
            const input = document.getElementById(inputId);

            if (input) {
                input.value = cantidad;
                console.log(`Actualizado input ${inputId} con valor ${cantidad}`);
                // Recalcular el total de la fila
                calcularTotalFila(input);
            } else {
                console.warn(`No se encontró input con ID: ${inputId} para semana ${semana}, trampa ${trampaId}, tipoIndex ${tipoIndex}`);
                // Intentar buscar de otra forma - usando selector de atributos
                const inputAlt = document.querySelector(`input[data-semana="${semana}"][data-trampa-id="${trampaId}"][data-tipo-index="${tipoIndex}"]`);
                if (inputAlt) {
                    inputAlt.value = cantidad;
                    calcularTotalFila(inputAlt);
                    console.log(`Encontrado input alternativo y actualizado`);
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
