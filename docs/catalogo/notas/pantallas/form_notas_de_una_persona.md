---
id: "notas.pantalla.form_notas_de_una_persona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Form Notas De Una Persona"
controller: "frontend/notas/controller/form_notas_de_una_persona.php"
vistas: ["frontend/notas/view/form_notas_de_una_persona.phtml"]
fragmentos_frontend: ["frontend/notas/controller/actividad_buscar_form.php"]
endpoints: ["/src/notas/buscar_acta", "/src/notas/nota_persona_form_data", "/src/notas/persona_nota_editar", "/src/notas/persona_nota_nueva", "/src/notas/posibles_opcionales_data", "/src/notas/posibles_preceptores_data"]
capacidades: ["notas.buscar_acta.gestionar", "notas.nota_persona.gestionar", "notas.persona_nota.gestionar", "notas.persona_nota_editar.gestionar", "notas.posibles_opcionales.gestionar", "notas.posibles_preceptores.gestionar"]
campos: ["form.acta", "form.dl_org", "form.f_acta_iso", "form.id_nom", "html.acta", "html.detalle", "html.epoca", "html.f_acta", "html.id_asignatura", "html.nota_max", "html.nota_num", "html.preceptor", "html.tipo_acta", "post.id_asignatura_real", "post.id_pau", "post.mod", "post.obj_pau", "post.pau", "post.permiso", "post.sel"]
acciones: ["fnjs_buscar_acta", "fnjs_buscar_ca", "fnjs_cerrar", "fnjs_cmb_opcional", "fnjs_cmb_preceptor", "fnjs_comprobar_fecha", "fnjs_construir_desplegable", "fnjs_guardar", "fnjs_modificar", "fnjs_nota", "fnjs_update_activ", "fnjs_update_div"]
estado_revision: "revisado"
---

# Form Notas De Una Persona

Dossier 1011: listado y formulario de notas de una persona (alta/edición/borrado).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/form_notas_de_una_persona.php`

## Vistas Relacionadas

- `frontend/notas/view/form_notas_de_una_persona.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/actividad_buscar_form.php`

## Endpoints Usados

- `/src/notas/buscar_acta`
- `/src/notas/nota_persona_form_data`
- `/src/notas/persona_nota_editar`
- `/src/notas/persona_nota_nueva`
- `/src/notas/posibles_opcionales_data`
- `/src/notas/posibles_preceptores_data`

## Capacidades Relacionadas

- `notas.buscar_acta.gestionar`
- `notas.nota_persona.gestionar`
- `notas.persona_nota.gestionar`
- `notas.persona_nota_editar.gestionar`
- `notas.posibles_opcionales.gestionar`
- `notas.posibles_preceptores.gestionar`

## Campos Detectados

- `form.acta`
- `form.dl_org`
- `form.f_acta_iso`
- `form.id_nom`
- `html.acta`
- `html.detalle`
- `html.epoca`
- `html.f_acta`
- `html.id_asignatura`
- `html.nota_max`
- `html.nota_num`
- `html.preceptor`
- `html.tipo_acta`
- `post.id_asignatura_real`
- `post.id_pau`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.sel`

## Acciones Detectadas

- `fnjs_buscar_acta`
- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_comprobar_fecha`
- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_nota`
- `fnjs_update_activ`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
