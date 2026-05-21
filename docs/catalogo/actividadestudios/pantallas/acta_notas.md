---
id: "actividadestudios.pantalla.acta_notas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Acta Notas"
controller: "frontend/actividadestudios/controller/acta_notas.php"
vistas: ["frontend/actividadestudios/view/acta_notas.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_ver.php"]
endpoints: ["/src/actividadestudios/acta_notas_data", "/src/actividadestudios/acta_notas_definitivas_grabar", "/src/actividadestudios/acta_notas_matricula_guardar"]
capacidades: ["actividadestudios.acta_notas.gestionar", "actividadestudios.acta_notas_definitivas_grabar.gestionar", "actividadestudios.acta_notas_matricula.gestionar"]
campos: ["form.acta_nota", "form.form_preceptor", "form.id_nom", "form.nota_max", "form.nota_num", "html.form_preceptor[]", "html.id_nom[]", "html.que", "post.id_activ", "post.id_asignatura", "post.id_nivel", "post.id_pau", "post.opcional", "post.primary_key_s", "post.que", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_enviar_formulario", "fnjs_guardar_nota", "fnjs_guardar_tessera", "fnjs_imprimir", "fnjs_left_side_hide", "fnjs_nota"]
estado_revision: "generado"
---

# Acta Notas

Pantalla del acta de notas para una asignatura concreta de una actividad.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/acta_notas.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/acta_notas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_imprimir.php`
- `frontend/notas/controller/acta_ver.php`

## Endpoints Usados

- `/src/actividadestudios/acta_notas_data`
- `/src/actividadestudios/acta_notas_definitivas_grabar`
- `/src/actividadestudios/acta_notas_matricula_guardar`

## Capacidades Relacionadas

- `actividadestudios.acta_notas.gestionar`
- `actividadestudios.acta_notas_definitivas_grabar.gestionar`
- `actividadestudios.acta_notas_matricula.gestionar`

## Campos Detectados

- `form.acta_nota`
- `form.form_preceptor`
- `form.id_nom`
- `form.nota_max`
- `form.nota_num`
- `html.form_preceptor[]`
- `html.id_nom[]`
- `html.que`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.opcional`
- `post.primary_key_s`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_guardar_nota`
- `fnjs_guardar_tessera`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_nota`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
