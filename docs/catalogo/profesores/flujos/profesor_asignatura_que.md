---
id: "profesores.profesor_asignatura_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Buscar profesores para una asignatura"
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

Saber qué profesores pueden impartir una asignatura.

## Punto De Entrada

Menú **vest > buscar persona > profesor para asignatura** (Pills2: stgr > personas).

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.profesor_asignatura_ajax`

## Escenarios Inferidos

### Buscar profesores para una asignatura

Pasos propuestos:
1. Entrar en «Profesores que pueden impartir una asignatura».
2. Elegir la **Asignatura** en el desplegable.
3. Debajo aparece la lista de profesores que pueden darla, o «no hay ninguna fila» si no hay ninguno registrado.

Endpoints asociados:
- `/src/profesores/profesor_asignatura_que`
- `/src/profesores/profesor_asignatura_ajax`

## Endpoints Del Flujo

- `/src/profesores/profesor_asignatura_que`
- `/src/profesores/profesor_asignatura_ajax`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > buscar persona > profesor para asignatura; stgr > personas > profesor para asignatura
- **Pills2:** ESTUDIOS > Preparación planes estudio > Profesor para asignatura
