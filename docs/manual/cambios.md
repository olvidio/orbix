---
tipo: manual_usuario
modulo: cambios
flujos: 12
estado_revision: revisado_parcial
---

# Manual De Usuario - cambios

Avisos de **cambios** en datos Orbix y preferencias de notificacion por usuario.

## Acceso Por Menu

| Texto en menu | Controller / ruta | Uso |
|---------------|-------------------|-----|
| **Lista de cambios** / **Ver lista cambios** | `avisos_generar.php` | Bandeja de avisos pendientes |
| **Generar tabla avisos** | CLI `avisos_generar_tabla.php` | Proceso batch (admin) |
| Preferencias avisos | `usuario_avisos_pref.php` | Configurar que avisos recibir |
| Listado por usuario | `usuario_form_avisos.php` | Avisos filtrados por usuario |

Roles habituales: **13** (admin avisos), **1**, **20**.

## Ver Lista De Cambios (Avisos)

### Para Que Sirve

Consultar cambios registrados en el sistema y **eliminar** avisos ya revisados (por seleccion o hasta fecha).

### Tareas Habituales

1. Abrir **Ver lista cambios**.
2. Revisar tabla de avisos.
3. Marcar filas y eliminar, o usar **eliminar hasta fecha** si esta disponible.
4. Confirmar operacion.

### Errores Frecuentes

- Mensajes de borrado en alert **respuesta:** — anotar texto si persiste.

Endpoints: `cambio_usuario_eliminar`, `cambio_usuario_eliminar_hasta_fecha` (AJAX desde pantalla, no controller dedicado en generador).

## Configurar Preferencias De Avisos

### Para Que Sirve

Definir **que objetos y propiedades** generan aviso para el usuario: condiciones, fases (vinculado a **procesos**), preview antes de guardar.

### Tareas Habituales

1. Abrir formulario de preferencias (`usuario_avisos_pref`).
2. Elegir objeto y propiedades (paneles AJAX: propiedades, fases, item).
3. Opcional: **vista previa** (`cambio_usuario_propiedad_pref_preview`).
4. **Guardar** objeto o **Eliminar** regla existente.
5. **Guardar todas** si aplica cambio masivo de propiedades.

### Modulos relacionados

- **procesos** — fases en `cambio_usuario_objeto_pref_fases_data`
- **usuarios** — contexto por usuario

## Revision Pendiente

- Documentar permisos exactos rol 13 vs usuario final.
- CLI `avisos_generar_mails` solo admin (no manual usuario).
