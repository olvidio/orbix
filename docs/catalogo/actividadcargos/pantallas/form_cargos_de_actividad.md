---
id: "actividadcargos.pantalla.form_cargos_de_actividad"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadcargos"
nombre: "Form Cargos De Actividad"
controller: "frontend/actividadcargos/controller/form_cargos_de_actividad.php"
vistas: ["frontend/actividadcargos/view/form_cargos_de_actividad.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadcargos/form_cargos_de_actividad_data"]
capacidades: ["actividadcargos.form_cargos_de_actividad.gestionar"]
campos: ["html.asis", "html.asis_presente", "html.cancel", "html.guardar", "html.observ", "html.puede_agd"]
acciones: ["fnjs_cancelar", "fnjs_cargo_de_actividad_datos_ok", "fnjs_guardar_cargo_act"]
estado_revision: "generado"
---

# Form Cargos De Actividad

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadcargos/controller/form_cargos_de_actividad.php`

## Vistas Relacionadas

- `frontend/actividadcargos/view/form_cargos_de_actividad.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadcargos/form_cargos_de_actividad_data`

## Capacidades Relacionadas

- `actividadcargos.form_cargos_de_actividad.gestionar`

## Campos Detectados

- `html.asis`
- `html.asis_presente`
- `html.cancel`
- `html.guardar`
- `html.observ`
- `html.puede_agd`

## Acciones Detectadas

- `fnjs_cancelar`
- `fnjs_cargo_de_actividad_datos_ok`
- `fnjs_guardar_cargo_act`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
