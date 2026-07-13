---
id: "profesores.profesor_asignatura_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Buscar profesor para asignatura"
capacidad: "profesores.profesor_asignatura_que.gestionar"
pantallas_principales: ["profesores.pantalla.profesor_asignatura_que"]
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
acciones: ["consultar"]
endpoints: ["/src/profesores/profesor_asignatura_que", "/src/profesores/profesor_asignatura_ajax"]
estado_revision: "revisado"
---

# Flujo - Buscar profesor para asignatura

Consulta de profesores habilitados para impartir una asignatura concreta.

## Objetivo De Usuario

Elegir asignatura y ver candidatos (departamento + ampliación) con datos de contacto y docencia
previa, como apoyo antes de asignar en el curso académico.

## Punto De Entrada

Pantalla `profesor_asignatura_que` (`frontend/profesores/controller/profesor_asignatura_que.php`).

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.profesor_asignatura_ajax`

## Escenarios Inferidos

### Consultar

Pasos:
1. Abrir **profesor para asignatura** desde el menú.
2. Elegir asignatura en el desplegable (`fnjs_profes`).
3. Revisar la tabla AJAX con profesores, centro, docencia y contacto.

Endpoints asociados:
- `/src/profesores/profesor_asignatura_que` — carga inicial
- `/src/profesores/profesor_asignatura_ajax` — tabla al cambiar asignatura

## Endpoints Del Flujo

- `/src/profesores/profesor_asignatura_que`
- `/src/profesores/profesor_asignatura_ajax`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > buscar persona > profesor para asignatura; stgr > personas > profesor para asignatura
- **Pills2:** ESTUDIOS > Preparación planes estudio > Profesor para asignatura
