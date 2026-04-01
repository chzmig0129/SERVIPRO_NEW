<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
    .estado-trampas-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .estado-trampas-table th {
        background-color: #4472C4;
        color: white;
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #345B8E;
        white-space: nowrap;
    }
    
    .estado-trampas-table td {
        padding: 10px;
        border: 1px solid #d1d5db;
        vertical-align: middle;
    }
    
    .estado-trampas-table td:first-child,
    .estado-trampas-table td:nth-child(2) {
        text-align: left;
        background-color: #f9fafb;
        font-weight: 500;
    }
    
    .estado-select {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
        background-color: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .estado-select:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .estado-select.funciona {
        border-color: #10b981;
        background-color: #f0fdf4;
    }
    
    .estado-select.en_reparacion {
        border-color: #f59e0b;
        background-color: #fffbeb;
    }
    
    .estado-select.no_funciona {
        border-color: #ef4444;
        background-color: #fef2f2;
    }
    
    .estado-select.sin_registro {
        border-color: #9ca3af;
        background-color: #f9fafb;
    }
    
    .observaciones-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        font-size: 14px;
        font-family: inherit;
        resize: vertical;
        min-height: 60px;
    }
    
    .observaciones-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .badge-estado {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-funciona {
        background-color: #d1fae5;
        color: #065f46;
    }
    
    .badge-en-reparacion {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .badge-no-funciona {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .badge-sin-registro {
        background-color: #f3f4f6;
        color: #4b5563;
    }
    
    .btn-guardar {
        background-color: #10b981;
        color: white;
        padding: 10px 24px;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-guardar:hover {
        background-color: #059669;
    }
    
    .btn-guardar:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }
    
    .alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: none;
    }
    
    .alert-success {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #10b981;
    }
    
    .alert-error {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }
    
    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }
    
    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .stat-card h3 {
        font-size: 14px;
        color: #6b7280;
        margin: 0 0 8px 0;
        font-weight: 500;
    }
    
    .stat-card .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #111827;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Registro de Estado de Trampas</h1>
            <p class="text-gray-500"><?= esc($plano['nombre']) ?> - <?= esc($sede['nombre']) ?></p>
        </div>
        <div class="flex gap-3">
            <button id="btnGuardarEstados" class="btn-guardar inline-flex items-center gap-2">
                <i class="fas fa-save"></i>
                Guardar Cambios
            </button>
            <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-arrow-left"></i>
                Volver al plano
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <div id="alertSuccess" class="alert alert-success">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="alertSuccessMessage">Los estados se han guardado correctamente.</span>
    </div>
    <div id="alertError" class="alert alert-error">
        <i class="fas fa-exclamation-circle mr-2"></i>
        <span id="alertErrorMessage">Error al guardar los estados.</span>
    </div>

    <!-- Estadísticas -->
    <div class="stats-container">
        <div class="stat-card">
            <h3>Total de Trampas</h3>
            <div class="stat-value" id="statTotal">0</div>
        </div>
        <div class="stat-card">
            <h3>Funcionando</h3>
            <div class="stat-value text-green-600" id="statFunciona">0</div>
        </div>
        <div class="stat-card">
            <h3>En Reparación</h3>
            <div class="stat-value text-yellow-600" id="statReparacion">0</div>
        </div>
        <div class="stat-card">
            <h3>No Funciona</h3>
            <div class="stat-value text-red-600" id="statNoFunciona">0</div>
        </div>
        <div class="stat-card">
            <h3>Sin Registro</h3>
            <div class="stat-value text-gray-600" id="statSinRegistro">0</div>
        </div>
    </div>

    <!-- Tabla de trampas -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Estado de Funcionamiento de las Trampas</h2>
        
        <?php if (empty($trampas)): ?>
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-info-circle text-4xl mb-4"></i>
                <p>No hay trampas registradas en este plano.</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="estado-trampas-table">
                    <thead>
                        <tr>
                            <th>ID Trampa</th>
                            <th>Tipo de Trampa</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="trampasTableBody">
                        <?php foreach ($trampas as $trampa): ?>
                            <tr data-trampa-id="<?= esc($trampa['trampa_id']) ?>">
                                <td><?= esc($trampa['id_trampa'] ?? 'N/A') ?></td>
                                <td><?= esc($trampa['tipo'] ?? 'N/A') ?></td>
                                <td><?= esc($trampa['ubicacion'] ?? 'N/A') ?></td>
                                <td>
                                    <select 
                                        class="estado-select <?= esc($trampa['estado'] ?? 'sin_registro') ?>" 
                                        name="estado[<?= esc($trampa['trampa_id']) ?>]"
                                        data-trampa-id="<?= esc($trampa['trampa_id']) ?>"
                                    >
                                        <option value="sin_registro" <?= ($trampa['estado'] ?? 'sin_registro') === 'sin_registro' ? 'selected' : '' ?>>Sin Registro</option>
                                        <option value="funciona" <?= ($trampa['estado'] ?? '') === 'funciona' ? 'selected' : '' ?>>Funciona</option>
                                        <option value="en_reparacion" <?= ($trampa['estado'] ?? '') === 'en_reparacion' ? 'selected' : '' ?>>En Reparación</option>
                                        <option value="no_funciona" <?= ($trampa['estado'] ?? '') === 'no_funciona' ? 'selected' : '' ?>>No Funciona</option>
                                    </select>
                                </td>
                                <td>
                                    <textarea 
                                        class="observaciones-input" 
                                        name="observaciones[<?= esc($trampa['trampa_id']) ?>]"
                                        placeholder="Ingrese observaciones sobre el estado de la trampa..."
                                        data-trampa-id="<?= esc($trampa['trampa_id']) ?>"
                                    ><?= esc($trampa['observaciones'] ?? '') ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnGuardar = document.getElementById('btnGuardarEstados');
    const alertSuccess = document.getElementById('alertSuccess');
    const alertError = document.getElementById('alertError');
    const estadoSelects = document.querySelectorAll('.estado-select');
    const observacionesInputs = document.querySelectorAll('.observaciones-input');
    
    // Actualizar estadísticas iniciales
    actualizarEstadisticas();
    
    // Cambiar clase del select cuando cambia el estado
    estadoSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Remover todas las clases de estado
            this.classList.remove('funciona', 'en_reparacion', 'no_funciona', 'sin_registro');
            // Agregar la clase correspondiente al nuevo estado
            this.classList.add(this.value);
            actualizarEstadisticas();
        });
    });
    
    // Función para actualizar estadísticas
    function actualizarEstadisticas() {
        const estados = {
            total: estadoSelects.length,
            funciona: 0,
            en_reparacion: 0,
            no_funciona: 0,
            sin_registro: 0
        };
        
        estadoSelects.forEach(select => {
            const estado = select.value;
            if (estados.hasOwnProperty(estado)) {
                estados[estado]++;
            }
        });
        
        document.getElementById('statTotal').textContent = estados.total;
        document.getElementById('statFunciona').textContent = estados.funciona;
        document.getElementById('statReparacion').textContent = estados.en_reparacion;
        document.getElementById('statNoFunciona').textContent = estados.no_funciona;
        document.getElementById('statSinRegistro').textContent = estados.sin_registro;
    }
    
    // Guardar estados
    btnGuardar.addEventListener('click', function() {
        const btnText = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="loading"></span> Guardando...';
        
        // Ocultar alertas anteriores
        alertSuccess.style.display = 'none';
        alertError.style.display = 'none';
        
        // Recopilar datos de todas las trampas
        const datos = [];
        estadoSelects.forEach(select => {
            const trampaId = select.dataset.trampaId;
            const observacionesInput = document.querySelector(`textarea[data-trampa-id="${trampaId}"]`);
            
            datos.push({
                trampa_id: trampaId,
                estado: select.value,
                observaciones: observacionesInput ? observacionesInput.value.trim() : ''
            });
        });
        
        // Enviar datos al servidor
        fetch('<?= base_url('blueprints/guardarEstadosTrampas/' . $plano['id']) ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                estados: datos,
                plano_id: <?= $plano['id'] ?>,
                sede_id: <?= $sede['id'] ?>
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alertSuccess.style.display = 'block';
                alertSuccessMessage.textContent = data.message || 'Los estados se han guardado correctamente.';
                
                // Scroll suave hacia la alerta
                alertSuccess.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                
                // Ocultar alerta después de 5 segundos
                setTimeout(() => {
                    alertSuccess.style.display = 'none';
                }, 5000);
            } else {
                alertError.style.display = 'block';
                alertErrorMessage.textContent = data.message || 'Error al guardar los estados.';
                
                // Scroll suave hacia la alerta
                alertError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alertError.style.display = 'block';
            alertErrorMessage.textContent = 'Error de conexión. Por favor, intente nuevamente.';
            alertError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = btnText;
        });
    });
});
</script>
<?= $this->endSection() ?>

