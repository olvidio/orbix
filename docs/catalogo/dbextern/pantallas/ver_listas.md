---
id: "dbextern.pantalla.ver_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Listas"
controller: "frontend/dbextern/controller/ver_listas.php"
vistas: ["frontend/dbextern/view/ver_listas.phtml"]
fragmentos_frontend: ["frontend/dbextern/controller/ver_listas.php"]
endpoints: ["/src/dbextern/sincro_crear", "/src/dbextern/sincro_crear_todos", "/src/dbextern/sincro_unir", "/src/dbextern/ver_listas_datos"]
capacidades: ["dbextern.sincro.gestionar", "dbextern.sincro_crear_todos.gestionar", "dbextern.sincro_unir.gestionar", "dbextern.ver_listas.gestionar"]
campos: ["form.dl", "form.id", "form.id_nom_listas", "form.id_orbix", "form.region", "form.tipo_persona", "html.mov", "post.dl", "post.id", "post.mov", "post.region", "post.tipo_persona"]
acciones: ["button:<", "fnjs_crear", "fnjs_crear_todos", "fnjs_enviar_formulario", "fnjs_submit", "fnjs_unir"]
estado_revision: "generado"
---

# Ver Listas

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_listas.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_listas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dbextern/controller/ver_listas.php`

## Endpoints Usados

- `/src/dbextern/sincro_crear`
- `/src/dbextern/sincro_crear_todos`
- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_listas_datos`

## Capacidades Relacionadas

- `dbextern.sincro.gestionar`
- `dbextern.sincro_crear_todos.gestionar`
- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_listas.gestionar`

## Campos Detectados

- `form.dl`
- `form.id`
- `form.id_nom_listas`
- `form.id_orbix`
- `form.region`
- `form.tipo_persona`
- `html.mov`
- `post.dl`
- `post.id`
- `post.mov`
- `post.region`
- `post.tipo_persona`

## Acciones Detectadas

- `button:<`
- `fnjs_crear`
- `fnjs_crear_todos`
- `fnjs_enviar_formulario`
- `fnjs_submit`
- `fnjs_unir`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
