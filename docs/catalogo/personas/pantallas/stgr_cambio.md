---
id: "personas.pantalla.stgr_cambio"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Cambio nivel STGR"
controller: "frontend/personas/controller/stgr_cambio.php"
vistas: ["frontend/personas/view/stgr_cambio.phtml"]
fragmentos_frontend: []
endpoints: ["/src/personas/stgr_cambio_data", "/src/personas/stgr_update"]
capacidades: ["personas.stgr.gestionar", "personas.stgr_cambio.gestionar"]
campos: ["form.nivel_stgr", "post.id_nom", "post.id_tabla", "post.sel"]
acciones: ["fnjs_guardar_stgr"]
estado_revision: "revisado"
---

# Cambio nivel STGR

Formulario modal con desplegable de niveles STGR para una persona seleccionada en el listado.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/stgr_cambio.php`

## Endpoints Usados

- `/src/personas/stgr_cambio_data`
- `/src/personas/stgr_update`

## Manual De Usuario

Pantalla revisada contra `frontend/personas/`. Requiere permiso `est` en el listado.

## Ruta de menú

- sin entrada de menú en el índice (botón «modificar stgr» en `personas_select`).
