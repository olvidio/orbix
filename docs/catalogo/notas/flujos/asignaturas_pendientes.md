---
id: "notas.asignaturas_pendientes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Consultar el cuadro alumnos-asignaturas"
capacidad: "notas.asignaturas_pendientes.gestionar"
pantallas_principales: ["notas.pantalla.asignaturas_pendientes"]
fragmentos: ["notas.pantalla.asignaturas_pendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asignaturas Pendientes

Propuesta generada automaticamente desde la capacidad `notas.asignaturas_pendientes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver el cuadro de alumnos y asignaturas pendientes de cada uno.

## Punto De Entrada

Menú **vest > actas... > tabla alumnos-asignaturas** (Pills2: ESTUDIOS > Preparación planes estudio).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asignaturas_pendientes`

## Escenarios Inferidos

### Consultar el cuadro alumnos-asignaturas

Pasos propuestos:
1. Entrar en **tabla alumnos-asignaturas**.
2. Se muestra una fila por alumno y una columna por asignatura, con lo que le falta.

Endpoints asociados:
- `/src/notas/asignaturas_pendientes_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `post.dl`

Acciones JavaScript:
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/asignaturas_pendientes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas
- **Pills2:** ESTUDIOS > Preparación planes estudio > Tab. Alumn/asig.; vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas
