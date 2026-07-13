---
id: "profesores.pantalla.profesor_asignatura_que"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "profesores"
nombre: "Profesor para asignatura"
controller: "frontend/profesores/controller/profesor_asignatura_que.php"
vistas: ["frontend/profesores/view/profesor_asignatura_que.phtml"]
fragmentos_frontend: ["frontend/profesores/controller/profesor_asignatura_ajax.php"]
endpoints: ["/src/profesores/profesor_asignatura_que", "/src/profesores/profesor_asignatura_ajax"]
capacidades: ["profesores.profesor_asignatura_que.gestionar"]
campos: ["form.id_asignatura"]
acciones: ["fnjs_left_side_hide", "fnjs_profes"]
estado_revision: "revisado"
---

# Profesor para asignatura

Formulario con desplegable de asignaturas; al elegir una, carga por AJAX la tabla de profesores
habilitados (departamento y ampliación) con contacto y docencia previa.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/profesores/controller/profesor_asignatura_que.php`

## Vistas Relacionadas

- `frontend/profesores/view/profesor_asignatura_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/profesores/controller/profesor_asignatura_ajax.php`

## Endpoints Usados

- `/src/profesores/profesor_asignatura_que` — opciones del desplegable
- `/src/profesores/profesor_asignatura_ajax` — tabla al cambiar asignatura

## Campos Detectados

- `form.id_asignatura` — asignatura seleccionada

## Acciones Detectadas

- `fnjs_profes` — recarga AJAX de profesores
- `fnjs_left_side_hide`

## Manual De Usuario

1. Abrir **profesor para asignatura** desde el menú.
2. Elegir asignatura en el desplegable.
3. Consultar profesores candidatos con centro, docencia y contacto.
4. Usar el resultado para asignar profesor en el dossier del curso.

## Ruta de menú

- **Legacy:** vest > buscar persona > profesor para asignatura; stgr > personas > profesor para asignatura
- **Pills2:** ESTUDIOS > Preparación planes estudio > Profesor para asignatura
