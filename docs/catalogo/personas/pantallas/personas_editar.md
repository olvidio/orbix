---
id: "personas.pantalla.personas_editar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "personas"
nombre: "Ficha de persona"
controller: "frontend/personas/controller/personas_editar.php"
vistas: ["frontend/personas/view/persona_form.phtml", "frontend/personas/view/persona_sss_form.phtml", "frontend/personas/view/persona_de_paso.phtml", "frontend/personas/view/p_public_personas.phtml"]
fragmentos_frontend: ["frontend/personas/view/_persona_form_js.phtml", "frontend/personas/view/_persona_form_botones.phtml"]
endpoints: ["/src/personas/personas_editar_data", "/src/personas/persona_update", "/src/personas/persona_eliminar"]
capacidades: ["personas.personas_editar.gestionar", "personas.persona.gestionar"]
campos: ["post.nuevo", "post.obj_pau", "post.sel", "post.apellido1", "post.tabla"]
acciones: ["fnjs_act_ctr", "fnjs_guardar", "fnjs_eliminar"]
estado_revision: "revisado"
---

# Ficha de persona

Alta (`nuevo=1`) o edición de persona. Plantilla según colectivo y permiso:

- `persona_form.phtml` — N, Agd, Nax, S (con permiso oficina o `dtor`)
- `persona_sss_form.phtml` — SSSC (`des`/`vcsd`/`dtor`)
- `persona_de_paso.phtml` — PersonaEx
- `p_public_personas.phtml` — solo lectura pública

Incluye enlace a traslado si no es alta.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/personas/controller/personas_editar.php`

## Endpoints Usados

- `/src/personas/personas_editar_data` (carga)
- `/src/personas/persona_update` (guardar)
- `/src/personas/persona_eliminar` (eliminar)

## Manual De Usuario

Pantalla revisada contra `frontend/personas/controller/personas_editar.php`.

## Ruta de menú

- sin entrada de menú en el índice (desde listado «ficha»/«nuevo» o cabecera persona).
