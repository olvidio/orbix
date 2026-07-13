---
id: "actividadestudios.pantalla.lista_clases_ca"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "Lista Clases Ca"
controller: "frontend/actividadestudios/controller/lista_clases_ca.php"
vistas: ["frontend/actividadestudios/view/lista_clases_ca.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/lista_clases_ca_data"]
capacidades: ["actividadestudios.lista_clases_ca.gestionar"]
campos: ["post.sel"]
acciones: []
estado_revision: "revisado"
---

# Lista Clases Ca

Listado imprimible de clases de un curso anual (CA): por cada asignatura muestra el profesor y la
lista numerada de alumnos matriculados con su centro.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/lista_clases_ca.php`

## Vistas Relacionadas

- `frontend/actividadestudios/view/lista_clases_ca.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/lista_clases_ca_data`

## Capacidades Relacionadas

- `actividadestudios.lista_clases_ca.gestionar`

## Campos Detectados

- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Se abre desde la selección de actividad en dossier (`sel` con `id_activ`). El controller resuelve
el id de actividad, mantiene la pila de navegación y consulta `lista_clases_ca_data`.

La vista muestra:

- Título con nombre del CA y director de estudios.
- Una tabla por asignatura: nombre, tipo y nombre del profesor, y alumnos matriculados numerados
  con su centro entre paréntesis.

Pantalla de solo consulta/imprimible, sin mutaciones.

## Ruta de menú

sin entrada de menú en el índice (vista de dossier / selección de actividad)
