---
id: "actividadestudios.pantalla.posibles_asignaturas_ca"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Posibles Asignaturas Ca"
controller: "frontend/actividadestudios/controller/posibles_asignaturas_ca.php"
vistas: ["frontend/actividadestudios/view/posibles_asignaturas_ca.html.twig"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/posibles_asignaturas_ca_data"]
capacidades: ["actividadestudios.posibles_asignaturas_ca.gestionar"]
campos: ["post.sel"]
acciones: ["fnjs_detalles"]
estado_revision: "revisado"
---

# Posibles Asignaturas Ca

Informe de asignaturas posibles por CA: cuántos alumnos pueden cursar cada asignatura y listado
de alumnos a los que les faltan pocas asignaturas para terminar el cuadrienio.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/posibles_asignaturas_ca.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/posibles_asignaturas_ca.html.twig`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/posibles_asignaturas_ca_data`

## Capacidades Relacionadas

- `actividadestudios.posibles_asignaturas_ca.gestionar`

## Campos Detectados

- `post.sel`

## Acciones Detectadas

- `fnjs_detalles`

## Manual De Usuario

Se abre desde selección de actividad en dossier (`sel` con `id_activ` y nombre). Consulta
`posibles_asignaturas_ca_data` y renderiza con Twig:

1. **Tabla principal:** por asignatura, número de posibles alumnos y enlace **ver** que despliega
   los nombres (`fnjs_detalles`).
2. **Segunda sección:** alumnos a los que les faltan 4 o menos asignaturas para terminar el
   cuadrienio, con el detalle de asignaturas pendientes.

Pantalla de consulta, sin mutaciones.

## Ruta de menú

sin entrada de menú en el índice (vista de dossier / selección de actividad)
