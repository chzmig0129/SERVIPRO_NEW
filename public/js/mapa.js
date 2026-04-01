// Variables globales
let puntos = [];
let actualizarTablaTrampas; // Declaramos la función como variable global

// Funciones globales
window.eliminarTrampa = function(index) {
    const marca = document.querySelector(`div[data-index="${index}"]`);
    
    if (marca) {
        marca.remove();
        puntos.splice(index, 1);
        
        // Actualizar los índices de las marcas restantes
        document.querySelectorAll('.trap-marker').forEach((marca, i) => {
            marca.dataset.index = i;
        });
        
        // Actualizar la tabla
        actualizarTablaTrampas();
    }
};

window.centrarEnTrampa = function(index) {
    const marca = document.querySelector(`div[data-index="${index}"]`);
    if (marca) {
        marca.scrollIntoView({ behavior: 'smooth', block: 'center' });
        marca.style.animation = 'highlight 2s';
        setTimeout(() => {
            marca.style.animation = '';
        }, 2000);
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const planoContainer = document.getElementById('planoContainer');
    const planoImage = document.getElementById('planoImage');
    const planoInput = document.getElementById('planoInput');
    const btnAgregarTrampa = document.getElementById('btnAgregarTrampa');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnCargar = document.getElementById('btnCargar');
    const cargarEstadoInput = document.getElementById('cargarEstadoInput');
    const btnLimpiar = document.getElementById('btnLimpiar');
    const btnSeleccionarImagen = document.getElementById('btnSeleccionarImagen');
    const btnMoverTrampa = document.getElementById('btnMoverTrampa');
    const btnZonaRectangulo = document.getElementById('btnZonaRectangulo');
    const btnZonaCirculo = document.getElementById('btnZonaCirculo');

    let modoEdicion = null;
    let trampaSeleccionada = null;
    let zonas = [];
    let zonaActual = null;
    let isResizing = false;
    let selectedTrapType = null;
    let contadorTrampas = 1; // Para generar IDs únicos

    // Asignar la función a la variable global
    actualizarTablaTrampas = function() {
        const tbody = document.getElementById('trampasTableBody');
        tbody.innerHTML = ''; // Limpiar tabla

        puntos.forEach((punto, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${punto.id || 'N/A'}</td>
                <td>${punto.nombre || 'Sin nombre'}</td>
                <td>
                    <i class="fas ${getIconoTrampa(punto.tipo)}"></i>
                    ${getNombreTipoTrampa(punto.tipo)}
                </td>
                <td>(${Math.round(punto.x)}, ${Math.round(punto.y)})</td>
                <td>
                    <input type="text" 
                           class="form-control form-control-sm zona-input" 
                           value="${punto.zona || ''}" 
                           placeholder="Ingrese la zona"
                           data-index="${index}">
                </td>
                <td>
                    <button class="btn btn-sm btn-danger" 
                            onclick="event.stopPropagation(); eliminarTrampa(${index});"
                            title="Eliminar trampa">
                        <i class="fas fa-trash"></i>
                    </button>
                    <button class="btn btn-sm btn-info" 
                            onclick="event.stopPropagation(); centrarEnTrampa(${index});"
                            title="Centrar en trampa">
                        <i class="fas fa-search"></i>
                    </button>
                </td>
            `;

            // Agregar clase para el hover y el cursor pointer
            tr.className = 'trap-row';
            
            // Agregar evento click a la fila
            tr.addEventListener('click', () => {
                resaltarTrampa(index);
            });

            tbody.appendChild(tr);
        });

        // Agregar eventos a los inputs de zona
        document.querySelectorAll('.zona-input').forEach(input => {
            // Evitar que el click en el input active el resaltado de la fila
            input.addEventListener('click', (e) => {
                e.stopPropagation();
            });

            // Guardar el valor cuando cambie
            input.addEventListener('change', (e) => {
                const index = parseInt(e.target.dataset.index);
                puntos[index].zona = e.target.value;
            });

            // Evitar que la tecla Enter dispare el click de la fila
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    input.blur();
                }
            });
        });
    };

    // Cargar imagen del plano
    btnSeleccionarImagen.addEventListener('click', function() {
        planoInput.click();
    });

    planoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Limpiar las trampas existentes antes de cargar nueva imagen
            const marcas = planoContainer.querySelectorAll('div');
            marcas.forEach(marca => marca.remove());
            puntos = [];
            
            const reader = new FileReader();
            reader.onload = function(e) {
                planoImage.src = e.target.result;
                planoImage.style.display = 'block';
                btnAgregarTrampa.disabled = false;
                btnGuardar.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });

    // Reemplazar el evento click del btnAgregarTrampa con esto:
    document.querySelectorAll('.trap-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const trapType = this.dataset.trapType;
            if (modoEdicion === 'agregarTrampa' && selectedTrapType === trapType) {
                modoEdicion = null;
                selectedTrapType = null;
                btnAgregarTrampa.classList.remove('active');
            } else {
                modoEdicion = 'agregarTrampa';
                selectedTrapType = trapType;
                btnAgregarTrampa.classList.add('active');
            }
        });
    });

    // Modificar el evento click del planoContainer
    planoContainer.addEventListener('click', function(e) {
        if (modoEdicion === 'agregarZonaRectangulo' || modoEdicion === 'agregarZonaCirculo') {
            const rect = planoContainer.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            crearZona(modoEdicion === 'agregarZonaCirculo' ? 'circulo' : 'rectangulo', x, y);
            modoEdicion = null;
        } else if (modoEdicion === 'agregarTrampa') {
            const rect = planoContainer.getBoundingClientRect();
            const imagen = planoImage.getBoundingClientRect();
            
            // Verificar si el clic está dentro de la imagen
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            
            // Calcular los límites de la imagen
            if (clickX >= imagen.left - rect.left && 
                clickX <= imagen.right - rect.left && 
                clickY >= imagen.top - rect.top && 
                clickY <= imagen.bottom - rect.top) {
                
                // Calcular la posición relativa dentro de la imagen
                const x = clickX - (imagen.left - rect.left);
                const y = clickY - (imagen.top - rect.top);
                
                // Crear el punto con el tipo de trampa
                const punto = {
                    x: x,
                    y: y,
                    tipo: selectedTrapType,
                    imagenWidth: imagen.width,
                    imagenHeight: imagen.height
                };
                
                puntos.push(punto);
                marcarTrampa(punto);
            }
        }
    });

    function marcarTrampa(punto) {
        const marca = document.createElement('div');
        marca.className = 'trap-marker';
        marca.style.position = 'absolute';
        marca.style.transform = 'translate(-50%, -50%)';
        
        // Agregar el icono según el tipo de trampa
        const icon = document.createElement('i');
        switch (punto.tipo) {
            case 'rodent':
                icon.className = 'fas fa-mouse';
                break;
            case 'insect':
                icon.className = 'fas fa-bug';
                break;
            case 'fly':
                icon.className = 'fas fa-fly';
                break;
            case 'moth':
                icon.className = 'fas fa-moth';
                break;
        }
        marca.appendChild(icon);
        
        // Calcular la posición absoluta basada en la posición actual de la imagen
        const imagen = planoImage.getBoundingClientRect();
        const container = planoContainer.getBoundingClientRect();
        
        const left = (imagen.left - container.left) + punto.x;
        const top = (imagen.top - container.top) + punto.y;
        
        marca.style.left = left + 'px';
        marca.style.top = top + 'px';

        // Agregar datos del punto a la marca
        marca.dataset.index = puntos.indexOf(punto);
        
        // Agregar evento click para mostrar coordenadas
        marca.addEventListener('click', function(e) {
            // Solo mostrar coordenadas si no estamos en modo de mover trampas
            if (modoEdicion !== 'moverTrampa') {
                mostrarCoordenadas(e, punto);
            }
        });
        
        planoContainer.appendChild(marca);

        // Mantener el evento mousedown para mover trampas
        marca.addEventListener('mousedown', function(e) {
            if (modoEdicion !== 'moverTrampa') return;
            trampaSeleccionada = {
                elemento: marca,
                punto: punto,
                offsetX: e.clientX - marca.getBoundingClientRect().left,
                offsetY: e.clientY - marca.getBoundingClientRect().top
            };
            e.preventDefault();
        });

        // Agregar ID único si no existe
        if (!punto.id) {
            punto.id = `T${contadorTrampas++}`;
        }
        
        // Actualizar la tabla después de agregar la trampa
        actualizarTablaTrampas();

        // Asegurarse de que el punto tenga una propiedad zona
        if (!punto.hasOwnProperty('zona')) {
            punto.zona = '';
        }
    }

    // Modificar la función mostrarCoordenadas
    function mostrarCoordenadas(event, punto) {
        // Eliminar tooltip existente si hay uno
        const tooltipExistente = document.querySelector('.tooltip-coordenadas');
        if (tooltipExistente) {
            tooltipExistente.remove();
        }

        // Crear nuevo tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip-coordenadas';
        tooltip.textContent = `Coordenadas: (${Math.round(punto.x)}, ${Math.round(punto.y)})`;

        // Obtener la posición del contenedor del plano
        const containerRect = planoContainer.getBoundingClientRect();

        // Obtener la posición de la trampa relativa al contenedor
        const trampa = event.target.closest('.trap-marker');
        const trampaRect = trampa.getBoundingClientRect();

        // Calcular la posición del tooltip relativa al contenedor
        tooltip.style.position = 'absolute';
        tooltip.style.left = (trampaRect.right - containerRect.left + 10) + 'px';
        tooltip.style.top = (trampaRect.top - containerRect.top + (trampaRect.height / 2)) + 'px';
        tooltip.style.transform = 'translateY(-50%)';

        // Agregar el tooltip al contenedor del plano
        planoContainer.appendChild(tooltip);

        // Eliminar tooltip después de 2 segundos
        setTimeout(() => {
            tooltip.remove();
        }, 2000);
    }

    // Agregar evento para ocultar tooltip cuando se hace click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tooltip-coordenadas') && !e.target.closest('div[data-index]')) {
            const tooltip = document.querySelector('.tooltip-coordenadas');
            if (tooltip) {
                tooltip.remove();
            }
        }
    });

    // Guardar estado
    btnGuardar.addEventListener('click', function() {
        const estado = {
            imagen: planoImage.src,
            trampas: puntos,
            zonas: zonas.map(zona => ({
                tipo: zona.tipo,
                x: zona.x,
                y: zona.y,
                width: zona.width,
                height: zona.height
            }))
        };
        const blob = new Blob([JSON.stringify(estado)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'estado-plano.json';
        a.click();
    });

    // Evento para cargar estado
    btnCargar.addEventListener('click', function() {
        cargarEstadoInput.click();
    });

    // Modificar el evento de carga de estado
    cargarEstadoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const estado = JSON.parse(e.target.result);
                
                // Limpiar el estado actual
                limpiarEstado();
                
                // Verificar si la imagen es una URL o datos base64
                if (estado.imagen) {
                    // Crear una nueva imagen para asegurar que se dispare el evento onload
                    const img = new Image();
                    img.onload = function() {
                        planoImage.src = img.src;
                        planoImage.style.display = 'block';
                        
                        // Cargar trampas después de que la imagen esté cargada
                        if (estado.trampas && Array.isArray(estado.trampas)) {
                            puntos = estado.trampas;
                            puntos.forEach(punto => marcarTrampa(punto));
                        }
                        
                        // Cargar zonas después de que la imagen esté cargada
                        if (estado.zonas && Array.isArray(estado.zonas)) {
                            estado.zonas.forEach(zona => {
                                const zonaCreada = crearZona(zona.tipo, zona.x, zona.y);
                                zonaCreada.elemento.style.width = zona.width + 'px';
                                zonaCreada.elemento.style.height = zona.height + 'px';
                                zonaCreada.width = zona.width;
                                zonaCreada.height = zona.height;
                            });
                        }
                        
                        btnAgregarTrampa.disabled = false;
                        btnGuardar.disabled = false;
                    };
                    
                    img.src = estado.imagen;
                } else {
                    console.error('No se encontró una imagen válida en el archivo JSON');
                    alert('El archivo JSON no contiene una imagen válida.');
                }
            } catch (error) {
                console.error('Error al cargar el estado:', error);
                alert('Error al cargar el archivo. Asegúrate de que sea un archivo JSON válido.');
            }
        };
        reader.readAsText(file);
    });

    // Agregar evento para el botón de mover trampas
    btnMoverTrampa.addEventListener('click', function() {
        if (modoEdicion === 'moverTrampa') {
            modoEdicion = null;
            btnMoverTrampa.classList.remove('active');
        } else {
            modoEdicion = 'moverTrampa';
            btnMoverTrampa.classList.add('active');
            // Desactivar otros modos
            btnAgregarTrampa.classList.remove('active');
        }
    });

    // Modificar el evento mousemove para actualizar la tabla después de mover una trampa
    document.addEventListener('mousemove', function(e) {
        if (!trampaSeleccionada || modoEdicion !== 'moverTrampa') return;

        const container = planoContainer.getBoundingClientRect();
        const imagen = planoImage.getBoundingClientRect();

        // Calcular las nuevas coordenadas relativas a la imagen
        const newX = e.clientX - imagen.left;
        const newY = e.clientY - imagen.top;

        // Verificar si está dentro de los límites de la imagen
        if (newX >= 0 && newX <= imagen.width && newY >= 0 && newY <= imagen.height) {
            // Actualizar posición del elemento visual
            trampaSeleccionada.elemento.style.left = (e.clientX - container.left) + 'px';
            trampaSeleccionada.elemento.style.top = (e.clientY - container.top) + 'px';

            // Actualizar coordenadas en el array de puntos
            trampaSeleccionada.punto.x = newX;
            trampaSeleccionada.punto.y = newY;
        }
    });

    // Modificar el evento mouseup para actualizar la tabla cuando se suelta la trampa
    document.addEventListener('mouseup', function() {
        if (trampaSeleccionada) {
            actualizarTablaTrampas(); // Actualizar la tabla cuando se suelta la trampa
        }
        trampaSeleccionada = null;
    });

    // Modificar la función limpiarEstado
    function limpiarEstado() {
        // Limpiar la imagen
        planoImage.src = '';
        planoImage.style.display = 'none';
        
        // Resetear los input files
        planoInput.value = '';
        cargarEstadoInput.value = '';
        
        // Resetear los arrays y el modo de edición
        puntos = [];
        zonas = [];
        modoEdicion = null;
        btnAgregarTrampa.classList.remove('active');
        btnMoverTrampa.classList.remove('active');
        
        // Deshabilitar botones
        btnAgregarTrampa.disabled = true;
        btnGuardar.disabled = true;
        
        // Eliminar todas las marcas de trampas y zonas
        const elementos = planoContainer.querySelectorAll('div');
        elementos.forEach(elemento => elemento.remove());

        contadorTrampas = 1; // Resetear el contador
        actualizarTablaTrampas(); // Limpiar la tabla
    }

    // Agregar evento para el botón de limpiar
    btnLimpiar.addEventListener('click', limpiarEstado);

    // Función para crear una nueva zona
    function crearZona(tipo, x, y) {
        const zona = document.createElement('div');
        zona.className = 'zona' + (tipo === 'circulo' ? ' zona-circulo' : '');
        zona.style.left = x + 'px';
        zona.style.top = y + 'px';
        zona.style.width = '100px';
        zona.style.height = '100px';

        // Agregar manejador de redimensión
        const resizeHandle = document.createElement('div');
        resizeHandle.className = 'resize-handle';
        resizeHandle.style.bottom = '-5px';
        resizeHandle.style.right = '-5px';
        zona.appendChild(resizeHandle);

        planoContainer.appendChild(zona);

        const zonaInfo = {
            elemento: zona,
            tipo: tipo,
            x: x,
            y: y,
            width: 100,
            height: 100
        };

        zonas.push(zonaInfo);
        return zonaInfo;
    }

    // Eventos para agregar zonas
    btnZonaRectangulo.addEventListener('click', function() {
        modoEdicion = 'agregarZonaRectangulo';
        desactivarOtrosModos(['btnAgregarTrampa', 'btnMoverTrampa']);
    });

    btnZonaCirculo.addEventListener('click', function() {
        modoEdicion = 'agregarZonaCirculo';
        desactivarOtrosModos(['btnAgregarTrampa', 'btnMoverTrampa']);
    });

    // Eventos para mover y redimensionar zonas
    document.addEventListener('mousedown', function(e) {
        const zona = e.target.closest('.zona');
        if (!zona) return;

        if (e.target.classList.contains('resize-handle')) {
            isResizing = true;
            zonaActual = zonas.find(z => z.elemento === zona);
            e.preventDefault();
        } else if (zona) {
            zonaActual = zonas.find(z => z.elemento === zona);
            zonaActual.offsetX = e.clientX - zona.offsetLeft;
            zonaActual.offsetY = e.clientY - zona.offsetTop;
        }
    });

    document.addEventListener('mousemove', function(e) {
        if (!zonaActual) return;

        if (isResizing) {
            const rect = planoContainer.getBoundingClientRect();
            const newWidth = e.clientX - zonaActual.elemento.offsetLeft - rect.left;
            const newHeight = e.clientY - zonaActual.elemento.offsetTop - rect.top;

            if (newWidth > 20 && newHeight > 20) {
                zonaActual.elemento.style.width = newWidth + 'px';
                zonaActual.elemento.style.height = newHeight + 'px';
                zonaActual.width = newWidth;
                zonaActual.height = newHeight;
            }
        } else {
            const rect = planoContainer.getBoundingClientRect();
            const newX = e.clientX - zonaActual.offsetX;
            const newY = e.clientY - zonaActual.offsetY;

            zonaActual.elemento.style.left = newX + 'px';
            zonaActual.elemento.style.top = newY + 'px';
            zonaActual.x = newX;
            zonaActual.y = newY;
        }
    });

    document.addEventListener('mouseup', function() {
        zonaActual = null;
        isResizing = false;
    });

    function desactivarOtrosModos(botones) {
        botones.forEach(btnId => {
            document.getElementById(btnId).classList.remove('active');
        });
    }

    // Función para obtener el nombre descriptivo del tipo de trampa
    function getNombreTipoTrampa(tipo) {
        switch (tipo) {
            case 'rodent':
                return 'Trampa para Roedores';
            case 'insect':
                return 'Trampa para Insectos';
            case 'fly':
                return 'Trampa para Moscas';
            case 'moth':
                return 'Trampa para Polillas';
            default:
                return 'Tipo Desconocido';
        }
    }

    // Función auxiliar para obtener el icono según el tipo
    function getIconoTrampa(tipo) {
        switch (tipo) {
            case 'rodent':
                return 'fa-mouse';
            case 'insect':
                return 'fa-bug';
            case 'fly':
                return 'fa-fly';
            case 'moth':
                return 'fa-moth';
            default:
                return 'fa-question';
        }
    }

    // Función para resaltar trampa
    function resaltarTrampa(index) {
        // Remover resaltado anterior
        document.querySelectorAll('.trap-marker').forEach(marca => {
            marca.classList.remove('highlighted');
        });
        document.querySelectorAll('.trap-row').forEach(row => {
            row.classList.remove('selected');
        });

        // Resaltar la trampa seleccionada
        const marca = document.querySelector(`div[data-index="${index}"]`);
        const row = document.querySelectorAll('.trap-row')[index];
        
        if (marca && row) {
            marca.classList.add('highlighted');
            row.classList.add('selected');
            
            // Asegurar que la trampa es visible en el plano
            marca.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
});

// Funciones existentes del mapa.js
function guardarEstado() {
    // Tu código existente
}

function limpiarTodo() {
    // Tu código existente
}

function toggleMoverTrampas() {
    // Tu código existente
}

function agregarTrampa(tipo) {
    // Tu código existente
}

function actualizarTablaTrampas() {
    // Tu código existente
}