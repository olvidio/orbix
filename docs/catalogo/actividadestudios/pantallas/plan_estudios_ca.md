---
id: "actividadestudios.pantalla.plan_estudios_ca"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Plan Estudios Ca"
controller: "frontend/actividadestudios/controller/plan_estudios_ca.php"
vistas: ["frontend/actividadestudios/view/plan_estudios_ca.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/plan_estudios_ca_data"]
capacidades: ["actividadestudios.plan_estudios_ca.gestionar"]
campos: ["post.sel"]
acciones: []
estado_revision: "revisado"
---

# Plan Estudios Ca

Vista del plan de estudios de un curso anual (CA): profesores, preceptores y alumnos con las
asignaturas matriculadas y créditos.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/plan_estudios_ca.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/plan_estudios_ca.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/plan_estudios_ca_data`

## Capacidades Relacionadas

- `actividadestudios.plan_estudios_ca.gestionar`

## Campos Detectados

- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Se abre desde la selección de actividad en dossier (`sel` → `id_activ`). Consulta
`plan_estudios_ca_data` y muestra:

- Nombre del CA y director de estudios.
- Bloque de profesores (asignatura, créditos, nombre).
- Bloque de preceptores (misma estructura).
- Lista de alumnos con centro, observaciones de estudios si las hay, y asignaturas matriculadas
  con créditos y preceptor.

Pantalla de solo consulta/imprimible.

## Ruta de menú

sin entrada de menú en el índice (vista de dossier / selección de actividad)
