---
id: "actividadestudios.pantalla.matriculas_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Matriculas Lista"
controller: "frontend/actividadestudios/controller/matriculas_lista.php"
vistas: ["frontend/actividadestudios/view/matriculas.phtml"]
fragmentos_frontend: ["frontend/actividadestudios/controller/matriculas_lista.php", "frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matriculas_lista_data"]
capacidades: ["actividadestudios.matricula.gestionar", "actividadestudios.matriculas.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.iactividad_val", "form.iasistentes_val", "form.periodo", "form.year", "html.mod", "html.pau", "html.refresh", "post.empiezamax", "post.empiezamin", "post.mod", "post.periodo", "post.stack", "post.year"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_buscar", "fnjs_enviar_formulario", "fnjs_left_side_hide", "fnjs_solo_uno", "fnjs_update_div", "fnjs_ver_ca"]
estado_revision: "generado"
---

# Matriculas Lista

Listado de matrículas (dossier).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/matriculas_lista.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/matriculas.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/matriculas_lista.php`
- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matriculas_lista_data`

## Capacidades Relacionadas

- `actividadestudios.matricula.gestionar`
- `actividadestudios.matriculas.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mod`
- `post.periodo`
- `post.stack`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
