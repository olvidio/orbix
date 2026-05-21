---
id: "dbextern.pantalla.ver_orbix"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "dbextern"
nombre: "Ver Orbix"
controller: "frontend/dbextern/controller/ver_orbix.php"
vistas: ["frontend/dbextern/view/ver_orbix.phtml"]
fragmentos_frontend: ["frontend/dbextern/controller/ver_orbix.php"]
endpoints: ["/src/dbextern/sincro_unir", "/src/dbextern/ver_orbix_datos"]
capacidades: ["dbextern.sincro_unir.gestionar", "dbextern.ver_orbix.gestionar"]
campos: ["form.dl", "form.id", "form.id_nom_listas", "form.id_orbix", "form.region", "form.tipo_persona", "html.mov", "post.dl", "post.id", "post.mov", "post.region", "post.tipo_persona"]
acciones: ["button:<", "fnjs_enviar_formulario", "fnjs_submit", "fnjs_unir_bdu"]
estado_revision: "generado"
---

# Ver Orbix

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/dbextern/controller/ver_orbix.php`

## Vistas Relacionadas

- `frontend/dbextern/view/ver_orbix.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dbextern/controller/ver_orbix.php`

## Endpoints Usados

- `/src/dbextern/sincro_unir`
- `/src/dbextern/ver_orbix_datos`

## Capacidades Relacionadas

- `dbextern.sincro_unir.gestionar`
- `dbextern.ver_orbix.gestionar`

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
- `fnjs_enviar_formulario`
- `fnjs_submit`
- `fnjs_unir_bdu`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
