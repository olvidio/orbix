---
id: "configuracion.pantalla.modulos_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "configuracion"
nombre: "Ficha de módulo"
controller: "frontend/configuracion/controller/modulos_form.php"
vistas: ["frontend/configuracion/view/modulos_form.phtml"]
fragmentos_frontend: ["frontend/configuracion/controller/modulos_update.php"]
endpoints: ["/src/configuracion/modulos_form_data", "/src/configuracion/modulos_update"]
capacidades: ["configuracion.modulos.gestionar"]
campos: ["html.nom", "html.descripcion", "html.sel_mods[]", "html.sel_apps[]", "html.id_mod", "html.mod", "post.refresh"]
acciones: ["fnjs_actualizar", "fnjs_cambio", "fnjs_enviar_formulario", "fnjs_guardar"]
estado_revision: "revisado"
---

# Ficha de módulo

Formulario de alta o edición de un módulo: nombre, descripción, checkboxes de módulos
requeridos y aplicaciones requeridas. Las apps heredadas de módulos requeridos aparecen
marcadas y deshabilitadas (`a_apps_mod`).

## Tipo

- Subtipo: `fragmento_ajax` (destino desde `modulos_select`, no entrada de menú propia)
- Controller: `frontend/configuracion/controller/modulos_form.php`

## Vistas Relacionadas

- `frontend/configuracion/view/modulos_form.phtml`

## Endpoints Usados

- `/src/configuracion/modulos_form_data` — datos del formulario y hashes (`hash_main`, `hash_actualizar`)
- `/src/configuracion/modulos_update` — persistencia vía `fnjs_guardar` / `fnjs_cambio`

## Flujo en pantalla

1. Alta (`mod=nuevo`) o edición (desde fila seleccionada en listado, `sel[]` → `id_mod`).
2. Cambiar checkboxes de módulos/apps dispara `fnjs_cambio` (guardado parcial AJAX).
3. «Guardar cambios» → `fnjs_guardar` → vuelve al listado (`navAtras`).
4. Formulario auxiliar `#frm_actualizar` permite refrescar la ficha tras crear registro.

## Manual De Usuario

1. Llegar desde «añadir módulo» o «modificar» en el listado de módulos.
2. Rellenar nombre y descripción.
3. Marcar módulos y aplicaciones requeridos (las apps de módulos dependientes se bloquean solas).
4. Guardar; volver al listado comprobando el nuevo registro o los cambios.

## Ruta de menú

Sin entrada de menú en el índice (acceso desde `modulos_select.php`).
