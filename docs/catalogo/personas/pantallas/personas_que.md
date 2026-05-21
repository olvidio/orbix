---
id: "personas.pantalla.personas_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "personas"
nombre: "Personas Que"
controller: "frontend/personas/controller/personas_que.php"
vistas: ["frontend/personas/view/personas_que.phtml"]
fragmentos_frontend: ["frontend/personas/controller/personas_que.php", "frontend/personas/controller/personas_select.php"]
endpoints: []
capacidades: []
campos: ["form.apellido1", "form.apellido2", "form.centro", "form.cmb", "form.exacto", "form.nombre", "html.apellido1", "html.apellido2", "html.btn_ok", "html.centro", "html.cmb", "html.exacto", "html.nombre", "post.apellido1", "post.apellido2", "post.centro", "post.cmb", "post.es_sacd", "post.exacto", "post.na", "post.nombre", "post.que", "post.stack", "post.tabla", "post.tipo"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario", "fnjs_update_div"]
estado_revision: "generado"
---

# Personas Que

Formulario de busqueda de personas.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/personas/controller/personas_que.php`

## Vistas Relacionadas

- `frontend/personas/view/personas_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/personas/controller/personas_que.php`
- `frontend/personas/controller/personas_select.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.apellido1`
- `form.apellido2`
- `form.centro`
- `form.cmb`
- `form.exacto`
- `form.nombre`
- `html.apellido1`
- `html.apellido2`
- `html.btn_ok`
- `html.centro`
- `html.cmb`
- `html.exacto`
- `html.nombre`
- `post.apellido1`
- `post.apellido2`
- `post.centro`
- `post.cmb`
- `post.es_sacd`
- `post.exacto`
- `post.na`
- `post.nombre`
- `post.que`
- `post.stack`
- `post.tabla`
- `post.tipo`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
