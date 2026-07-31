---
id: "personas.pantalla.persona_publicar_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Publicar persona"
controller: "frontend/personas/controller/persona_publicar_form.php"
vistas: ["frontend/personas/view/persona_publicar.phtml"]
fragmentos_frontend: []
endpoints: ["/src/personas/persona_publicar_form_data", "/src/personas/persona_publicar"]
capacidades: ["personas.persona_publicar.gestionar"]
campos: ["form.dl", "post.id_nom", "post.id_tabla", "post.id_schema", "post.sel"]
acciones: ["fnjs_guardar_publicar"]
estado_revision: "revisado"
---

# Publicar persona

Formulario para hacer visible una persona en el desplegable de otra DL durante un mes (caso B /
`publicado_para`). Se abre desde el listado de personas con el botón «publicar».

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/persona_publicar_form.php`

## Vistas Relacionadas

- `frontend/personas/view/persona_publicar.phtml`

## Endpoints Usados

- `/src/personas/persona_publicar_form_data` (carga nombre, `id_schema`, opciones DL)
- `/src/personas/persona_publicar` (guardar)

## Capacidades Relacionadas

- `personas.persona_publicar.gestionar`

## Campos Detectados

- `form.dl` (desplegable de DL destino)
- Hidden HashFront: `id_nom`, `id_tabla`, `id_schema`

## Acciones Detectadas

- `fnjs_guardar_publicar` — POST AJAX a `persona_publicar`; en éxito alerta y vuelve atrás

## Manual De Usuario

1. En el listado de personas, seleccionar una fila y pulsar «publicar» (visible con permiso
   `est`/`sm`/`agd`).
2. Elegir la delegación destino en el desplegable.
3. Pulsar «publicar». La persona queda visible en esa DL durante un mes.

## Ruta de menú

- sin entrada de menú en el índice (desde listado personas → «publicar»).
