---
id: "profesores.pantalla.profesor_asignatura_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "profesores"
nombre: "Tabla profesores por asignatura"
controller: "frontend/profesores/controller/profesor_asignatura_ajax.php"
vistas: ["frontend/profesores/view/profesor_asignatura_ajax.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
capacidades: ["profesores.profesor_asignatura_ajax.gestionar"]
campos: ["post.id_asignatura"]
acciones: []
estado_revision: "revisado"
---

# Tabla profesores por asignatura

Fragmento AJAX: devuelve HTML de tabla `Lista` con profesores del departamento y de ampliación para
la asignatura indicada.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/profesores/controller/profesor_asignatura_ajax.php`

## Endpoints Usados

- `/src/profesores/profesor_asignatura_ajax`

## Campos Detectados

- `post.id_asignatura`

## Manual De Usuario

Se carga automáticamente al cambiar el desplegable en **profesor para asignatura** (`fnjs_profes`).
No tiene entrada de menú propia.

## Ruta de menú

sin entrada de menú en el índice (fragmento AJAX de `profesor_asignatura_que`)
