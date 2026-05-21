---
id: "actividadestudios.pantalla.form_matriculas_de_una_persona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Form Matriculas De Una Persona"
controller: "frontend/actividadestudios/controller/form_matriculas_de_una_persona.php"
vistas: ["frontend/actividadestudios/view/form_matriculas_de_una_persona.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/form_matriculas_de_una_persona_data", "/src/actividadestudios/matricula_editar", "/src/actividadestudios/matricula_nueva", "/src/notas/posibles_opcionales_data", "/src/notas/posibles_preceptores_data"]
capacidades: ["actividadestudios.form_matriculas_de_una_persona.gestionar", "actividadestudios.matricula.gestionar", "actividadestudios.matricula_editar.gestionar"]
campos: ["form.id_nom", "html.id_asignatura", "html.preceptor", "post.id_activ", "post.id_asignatura", "post.id_nivel", "post.id_pau", "post.sel"]
acciones: ["fnjs_cmb_opcional", "fnjs_cmb_preceptor", "fnjs_construir_desplegable", "fnjs_guardar"]
estado_revision: "generado"
---

# Form Matriculas De Una Persona

Form de alta / edicion de una `Matricula` desde los dossiers `matriculas_de_una_persona` (1303) y `matriculas_de_una_actividad` (3103).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/form_matriculas_de_una_persona.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/form_matriculas_de_una_persona_data`
- `/src/actividadestudios/matricula_editar`
- `/src/actividadestudios/matricula_nueva`
- `/src/notas/posibles_opcionales_data`
- `/src/notas/posibles_preceptores_data`

## Capacidades Relacionadas

- `actividadestudios.form_matriculas_de_una_persona.gestionar`
- `actividadestudios.matricula.gestionar`
- `actividadestudios.matricula_editar.gestionar`

## Campos Detectados

- `form.id_nom`
- `html.id_asignatura`
- `html.preceptor`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.sel`

## Acciones Detectadas

- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
