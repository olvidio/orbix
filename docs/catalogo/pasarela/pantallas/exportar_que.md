---
id: "pasarela.pantalla.exportar_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Exportar Que"
controller: "frontend/pasarela/controller/exportar_que.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/exportar_select.php"]
endpoints: ["/src/pasarela/exportar_que_actividad_tipo_html"]
capacidades: ["pasarela.exportar_que_actividad_tipo_html.gestionar"]
campos: ["form.cdc_sel", "form.empiezamax", "form.empiezamin", "form.extendida", "form.iactividad_val", "form.iasistentes_val", "form.id_cdc", "form.id_cdc_mas", "form.id_cdc_num", "form.id_tipo_activ", "form.inom_tipo_val", "form.isfsv_val", "form.periodo", "form.year", "post.cdc_sel", "post.empiezamax", "post.empiezamin", "post.fin", "post.id_cdc_mas", "post.id_cdc_num", "post.id_tipo_activ", "post.inicio", "post.periodo", "post.sactividad", "post.sasistentes", "post.snom_tipo", "post.stack", "post.year"]
acciones: []
estado_revision: "generado"
---

# Exportar Que

Página para cambiar la fase a un grupo de actividades.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/exportar_que.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/exportar_select.php`

## Endpoints Usados

- `/src/pasarela/exportar_que_actividad_tipo_html`

## Capacidades Relacionadas

- `pasarela.exportar_que_actividad_tipo_html.gestionar`

## Campos Detectados

- `form.cdc_sel`
- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_cdc`
- `form.id_cdc_mas`
- `form.id_cdc_num`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `form.periodo`
- `form.year`
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.id_cdc_mas`
- `post.id_cdc_num`
- `post.id_tipo_activ`
- `post.inicio`
- `post.periodo`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`
- `post.stack`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
