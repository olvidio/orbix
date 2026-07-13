---
id: "actividadplazas.pantalla.peticiones_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadplazas"
nombre: "Peticiones Activ"
controller: "frontend/actividadplazas/controller/peticiones_activ.php"
vistas: ["frontend/actividadplazas/view/peticiones_activ.phtml"]
fragmentos_frontend: ["frontend/actividadplazas/controller/peticiones_activ.php"]
endpoints: ["/src/actividadplazas/peticiones_activ_data", "/src/actividadplazas/peticiones_eliminar", "/src/actividadplazas/peticiones_guardar"]
capacidades: ["actividadplazas.peticiones.gestionar", "actividadplazas.peticiones_activ.gestionar"]
campos: ["form.actividades", "form.actividades_mas", "form.actividades_num", "post.id_ctr_agd", "post.id_ctr_n", "post.id_nom", "post.na", "post.que", "post.sactividad", "post.sel", "post.stack", "post.todos"]
acciones: ["fnjs_actualizar", "fnjs_borrar", "fnjs_enviar_formulario", "fnjs_guardar", "fnjs_left_slide_atras", "fnjs_mas_actividades"]
estado_revision: "revisado"
---

# Peticiones Activ

Pantalla de peticiones de plaza de una persona (colectivos n / a / agd): permite ordenar las
actividades solicitadas y guardarlas o borrarlas.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadplazas/controller/peticiones_activ.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/peticiones_activ.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Endpoints Usados

- `/src/actividadplazas/peticiones_activ_data`
- `/src/actividadplazas/peticiones_eliminar`
- `/src/actividadplazas/peticiones_guardar`

## Capacidades Relacionadas

- `actividadplazas.peticiones.gestionar`
- `actividadplazas.peticiones_activ.gestionar`

## Campos Detectados

- `form.actividades`
- `form.actividades_mas`
- `form.actividades_num`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.id_nom`
- `post.na`
- `post.que`
- `post.sactividad`
- `post.sel`
- `post.stack`
- `post.todos`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_slide_atras`
- `fnjs_mas_actividades`

## Manual De Usuario

Muestra el nombre de la persona y un conjunto de desplegables (`DesplegableArray`) con las actividades
candidatas del tipo, precargados con las peticiones actuales en orden de prioridad. El usuario añade o
reordena actividades (`fnjs_mas_actividades`) y:

- **Guardar peticiones** (`fnjs_guardar`): envía a `peticiones_guardar` y vuelve atrás.
- **Borrar** (`fnjs_borrar`): envía a `peticiones_eliminar` y refresca (`fnjs_actualizar`).

La lista candidata proviene de `peticiones_activ_data`, que además limpia peticiones antiguas ya no
disponibles.

## Ruta de menú

- Sin entrada de menú en el índice: se abre desde la selección de una persona (n / a / agd), no
  directamente desde un menú.
