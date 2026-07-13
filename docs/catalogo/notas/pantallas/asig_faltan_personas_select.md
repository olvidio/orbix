---
id: "notas.pantalla.asig_faltan_personas_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Asig Faltan Personas Select"
controller: "frontend/notas/controller/asig_faltan_personas_select.php"
vistas: ["frontend/notas/view/asig_faltan_personas_select.phtml"]
fragmentos_frontend: ["frontend/notas/controller/tessera_ver.php", "frontend/personas/controller/home_persona.php", "frontend/personas/controller/stgr_cambio.php"]
endpoints: ["/src/notas/asig_faltan_personas_select_data"]
capacidades: ["notas.asig_faltan_personas_select.gestionar"]
campos: ["form.sel", "post.b_c", "post.c1", "post.c2", "post.id_asignatura", "post.personas_agd", "post.personas_n", "post.stack"]
acciones: ["fnjs_enviar_formulario", "fnjs_modificar", "fnjs_solo_uno", "fnjs_tesera"]
estado_revision: "revisado"
---

# Asig Faltan Personas Select

Resultado: alumnos a los que falta una asignatura concreta.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/asig_faltan_personas_select.php`

## Vistas Relacionadas

- `frontend/notas/view/asig_faltan_personas_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/tessera_ver.php`
- `frontend/personas/controller/home_persona.php`
- `frontend/personas/controller/stgr_cambio.php`

## Endpoints Usados

- `/src/notas/asig_faltan_personas_select_data`

## Capacidades Relacionadas

- `notas.asig_faltan_personas_select.gestionar`

## Campos Detectados

- `form.sel`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.id_asignatura`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_tesera`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
