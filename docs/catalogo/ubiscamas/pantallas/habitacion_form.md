---
id: "ubiscamas.pantalla.habitacion_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubiscamas"
nombre: "Habitacion Form"
controller: "frontend/ubiscamas/controller/habitacion_form.php"
vistas: ["frontend/ubiscamas/view/habitacion_form.phtml"]
fragmentos_frontend: ["frontend/ubiscamas/controller/habitacion_form.php"]
endpoints: ["/src/ubiscamas/habitacion_form_data"]
capacidades: ["ubiscamas.habitacion.gestionar"]
campos: ["html.adaptada", "html.despacho", "html.new_camas_desc[${rowIdx}]", "html.new_camas_larga[${rowIdx}]", "html.new_camas_vip[${rowIdx}]", "html.nombre", "html.numero_camas", "html.numero_camas_vip", "html.observaciones", "html.orden", "html.planta", "html.refresh", "html.sillon", "html.tipoLavabo", "post.refresh"]
acciones: ["fnjs_actualizar", "fnjs_anadir_cama_dinamica", "fnjs_cancelar", "fnjs_editar_cama", "fnjs_eliminar_cama", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_nueva_cama", "fnjs_update_div"]
estado_revision: "revisado"
---

# Habitacion Form

Formulario de alta/edición de habitación CDC con gestión inline de camas.

## Tipo

- Subtipo: `fragmento_ajax`

- Controller: `frontend/ubiscamas/controller/habitacion_form.php`

## Vistas Relacionadas

- `frontend/ubiscamas/view/habitacion_form.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubiscamas/controller/habitacion_form.php`

## Endpoints Usados

- `/src/ubiscamas/habitacion_form_data`

## Capacidades Relacionadas

- `ubiscamas.habitacion.gestionar`

## Campos Detectados

- `html.adaptada`
- `html.despacho`
- `html.new_camas_desc[${rowIdx}]`
- `html.new_camas_larga[${rowIdx}]`
- `html.new_camas_vip[${rowIdx}]`
- `html.nombre`
- `html.numero_camas`
- `html.numero_camas_vip`
- `html.observaciones`
- `html.orden`
- `html.planta`
- `html.refresh`
- `html.sillon`
- `html.tipoLavabo`
- `post.refresh`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_anadir_cama_dinamica`
- `fnjs_cancelar`
- `fnjs_editar_cama`
- `fnjs_eliminar_cama`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_nueva_cama`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
