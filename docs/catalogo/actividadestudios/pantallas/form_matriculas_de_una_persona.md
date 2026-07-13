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
estado_revision: "revisado"
---

# Form Matriculas De Una Persona

Formulario de alta o edición de una `Matricula` desde los dossiers `matriculas_de_una_persona`
(1303) y `matriculas_de_una_actividad` (3103). Sucesor de
`apps/actividadestudios/controller/form_1303.php`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/form_matriculas_de_una_persona.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/form_matriculas_de_una_persona.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/form_matriculas_de_una_persona_data` (carga del formulario)
- `/src/notas/posibles_opcionales_data` (`fnjs_cmb_opcional`)
- `/src/notas/posibles_preceptores_data` (`fnjs_cmb_preceptor`)
- `/src/actividadestudios/matricula_nueva` (`fnjs_guardar`, modo alta)
- `/src/actividadestudios/matricula_editar` (`fnjs_guardar`, modo edición)

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

Recibe `id_pau` (persona), `id_activ` y opcionalmente selección múltiple `sel`. Muestra la
actividad y:

- **Alta:** desplegable de nivel/asignatura; si la asignatura es opcional (`condicion_js`), al
  cambiar nivel carga opcionales vía `posibles_opcionales_data`.
- **Edición:** asignatura fija (`id_asignatura_real`).
- Checkbox preceptor: al marcarlo carga preceptores posibles (`posibles_preceptores_data`).

**Guardar** valida que haya asignatura (nivel ≥ 1000 en alta) y envía a `matricula_nueva` o
`matricula_editar`; con éxito regresa al dossier padre.

## Ruta de menú

sin entrada de menú en el índice (formulario de dossiers 1303 / 3103)
