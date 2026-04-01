# Actividades Registradas en el Sistema de Auditoría

Este documento lista todas las actividades que se registran automáticamente en el sistema de logs de auditoría.

## 📋 Resumen de Actividades

### 🔐 Autenticación (Home Controller)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `login` | Inicio de sesión exitoso | - |
| `login_failed` | Intento de inicio de sesión fallido | - |
| `logout` | Cierre de sesión | - |

**Ubicación:** `app/Controllers/Home.php`
- Método `authenticate()` - Registra login exitoso y fallido
- Método `logout()` - Registra cierre de sesión

---

### 🏢 Sedes (SedesController)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de una nueva sede | `sedes` |
| `disable` | Deshabilitación de una sede | `sedes` |

**Ubicación:** `app/Controllers/SedesController.php`
- Método `guardar()` - Registra creación de sedes
- Método `deshabilitar()` - Registra deshabilitación de sedes

**Ejemplo de log:**
- Creación: "Se creó una nueva sede: [Nombre de la sede]"
- Deshabilitación: "Se deshabilitó la sede: [Nombre de la sede]"

---

### 🐛 Quejas (QuejasController)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de una nueva queja | `quejas` |
| `update` | Actualización de una queja existente | `quejas` |
| `delete` | Eliminación de una queja | `quejas` |
| `status_change` | Cambio de estado de una queja | `quejas` |

**Ubicación:** `app/Controllers/QuejasController.php`
- Método `create()` - Registra creación de quejas
- Método `update()` - Registra actualización de quejas
- Método `delete()` - Registra eliminación de quejas
- Método `actualizarEstado()` - Registra cambio de estado

**Ejemplo de log:**
- Creación: "Se creó una nueva queja: [Insecto] en [Ubicación]"
- Actualización: "Se actualizó la queja ID: [ID]"
- Eliminación: "Se eliminó la queja ID: [ID] - [Insecto] en [Ubicación]"
- Cambio de estado: "Se cambió el estado de la queja ID: [ID] de [Estado Anterior] a [Estado Nuevo]"

---

### 💰 Ventas (VentasController)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de una nueva venta | `ventas` |
| `update` | Actualización de una venta existente | `ventas` |
| `delete` | Eliminación de una venta | `ventas` |

**Ubicación:** `app/Controllers/VentasController.php`
- Método `create()` - Registra creación de ventas
- Método `update()` - Registra actualización de ventas
- Método `delete()` - Registra eliminación de ventas

**Ejemplo de log:**
- Creación: "Se creó una nueva venta: [Concepto] por $[Monto]"
- Actualización: "Se actualizó la venta ID: [ID] - [Concepto]"
- Eliminación: "Se eliminó la venta ID: [ID] - [Concepto] por $[Monto]"

---

### 📄 Repositorio de Documentos (RepositorioController)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Subida de un documento al repositorio | `repositorio_documentos` |
| `delete` | Eliminación de un documento del repositorio | `repositorio_documentos` |

**Ubicación:** `app/Controllers/RepositorioController.php`
- Método `subir()` - Registra subida de documentos
- Método `eliminar()` - Registra eliminación de documentos

**Ejemplo de log:**
- Subida: "Se subió un documento: [Título] (tipo: [Tipo])"
- Eliminación: "Se eliminó el documento: [Título]"

---

### 📐 Planos (Blueprints Controller)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de un nuevo plano | `planos` |

**Ubicación:** `app/Controllers/Blueprints.php`
- Método `guardar_plano()` - Registra creación de planos

**Ejemplo de log:**
- Creación: "Se creó un nuevo plano: [Nombre] para la sede ID: [ID]"

---

### 📊 Incidencias

#### Incidencias Controller (Evidencias)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de una nueva incidencia/evidencia | `evidencias` |

**Ubicación:** `app/Controllers/Incidencias.php`
- Método `guardarIncidencia()` - Registra creación de incidencias/evidencias desde planos

**Ejemplo de log:**
- Creación: "Se creó una nueva incidencia/evidencia en el plano ID: [ID]"

#### Blueprints Controller (Incidencias desde Tablas y Excel)

| Acción | Descripción | Tabla Afectada |
|--------|-------------|----------------|
| `create` | Creación de una incidencia desde tabla o Excel | `incidencias` |
| `update` | Actualización de una incidencia existente | `incidencias` |

**Ubicación:** `app/Controllers/Blueprints.php`
- Método `guardar_incidencia()` - Registra creación de incidencias desde tablas configuradas o procesamiento de Excel
- Método `actualizar_incidencia()` - Registra actualización de incidencias

**Ejemplo de log:**
- Creación: "Se creó una incidencia: [Tipo Plaga] ([Tipo Insecto]) - Cantidad: [Cantidad] - Inspector: [Inspector]"
- Actualización: "Se actualizó la incidencia ID: [ID] - [Tipo Plaga] ([Tipo Insecto])"

**Nota:** Las incidencias registradas desde Excel o las tablas configuradas se guardan a través del método `guardar_incidencia()`, que procesa cada incidencia individualmente y registra cada una en el log de auditoría.

---

## 📊 Estadísticas de Cobertura

### Total de Actividades Registradas: **17**

- **Autenticación:** 3 actividades
- **Sedes:** 2 actividades
- **Quejas:** 4 actividades
- **Ventas:** 3 actividades
- **Repositorio:** 2 actividades
- **Planos:** 1 actividad
- **Incidencias:** 2 actividades
  - Evidencias desde planos: 1 actividad
  - Incidencias desde tablas/Excel: 2 actividades (create, update)

### Tipos de Acciones

- `create`: 8 operaciones
- `update`: 3 operaciones
- `delete`: 3 operaciones
- `login`: 2 operaciones
- `logout`: 1 operación
- `disable`: 1 operación
- `status_change`: 1 operación

---

## 🔍 Información Capturada Automáticamente

Para cada actividad registrada, el sistema captura automáticamente:

1. **Usuario:**
   - ID del usuario (desde la sesión)
   - Nombre del usuario (desde la sesión)

2. **Acción:**
   - Tipo de acción realizada
   - Tabla afectada (si aplica)
   - ID del registro afectado (si aplica)
   - Descripción detallada

3. **Datos:**
   - Datos anteriores (para updates)
   - Datos nuevos (para creates y updates)

4. **Contexto:**
   - Dirección IP del usuario
   - User Agent del navegador
   - Fecha y hora exacta de la acción

---

## 📝 Notas Importantes

1. **Los logs se registran solo después de operaciones exitosas.** Si una operación falla, no se registra en auditoría.

2. **Los logs incluyen información sensible de forma segura.** Las contraseñas nunca se registran.

3. **Todos los controladores que extienden `BaseController` tienen acceso automático al helper de auditoría.**

4. **Los logs se almacenan indefinidamente** hasta que se implemente un sistema de limpieza periódica.

---

## 🚀 Agregar Nuevas Actividades

Para agregar logs en nuevos controladores o acciones:

1. Asegúrate de que el controlador extienda `BaseController`
2. Agrega la llamada al helper apropiado después de la operación exitosa:

```php
// Para creación
log_create('tabla', $id, $datos, 'Descripción');

// Para actualización
log_update('tabla', $id, $datosAnteriores, $datosNuevos, 'Descripción');

// Para eliminación
log_delete('tabla', $id, $datosEliminados, 'Descripción');

// Para cambio de estatus
log_status_change('tabla', $id, $estadoAnterior, $estadoNuevo, 'Descripción');
```

---

**Última actualización:** Enero 2025

