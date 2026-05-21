---
id: "personas.pantalla.stgr_cambio"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Stgr Cambio"
controller: "frontend/personas/controller/stgr_cambio.php"
vistas: ["frontend/personas/view/stgr_cambio.phtml"]
fragmentos_frontend: []
endpoints: ["/src/personas/stgr_cambio_data", "/src/personas/stgr_update"]
capacidades: ["personas.stgr.gestionar", "personas.stgr_cambio.gestionar"]
campos: ["form.nivel_stgr", "html.guardar", "post.id_nom", "post.id_tabla", "post.sel"]
acciones: ["fnjs_guardar_stgr"]
estado_revision: "generado"
---

# Stgr Cambio

Formulario para cambiar el `nivel_stgr` de una persona.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/stgr_cambio.php`

## Vistas Relacionadas

- `frontend/personas/view/stgr_cambio.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/personas/stgr_cambio_data`
- `/src/personas/stgr_update`

## Capacidades Relacionadas

- `personas.stgr.gestionar`
- `personas.stgr_cambio.gestionar`

## Campos Detectados

- `form.nivel_stgr`
- `html.guardar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`

## Acciones Detectadas

- `fnjs_guardar_stgr`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
