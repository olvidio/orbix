---
id: "notas.asignaturas_pendientes_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Consultar el resumen de pendientes"
capacidad: "notas.asignaturas_pendientes_resumen.gestionar"
pantallas_principales: ["notas.pantalla.asignaturas_pendientes_resumen"]
fragmentos: ["notas.pantalla.asignaturas_pendientes_resumen"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Asignaturas Pendientes Resumen

Propuesta generada automaticamente desde la capacidad `notas.asignaturas_pendientes_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver, para cada asignatura, cuántos alumnos la tienen pendiente.

## Punto De Entrada

Menú **vest > actas... > resumen pendientes** (Pills2: ESTUDIOS > Preparación planes estudio > nº alum. por asignatura).

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asignaturas_pendientes_resumen`

## Escenarios Inferidos

### Consultar el resumen de pendientes

Pasos propuestos:
1. Entrar en **resumen pendientes**.
2. Cada fila es una asignatura (con sus créditos y año). Las columnas dan el número de alumnos que la tienen pendiente: las que empiezan por **n** son numerarios y las que empiezan por **a** son agregados. Después de la letra va el curso en que está el alumno: **b** = bienio, **c1** = cuadrienio año I, **c2** = cuadrienio año II. La columna **total** suma b + c1 + c2. Así, nb, nc1, nc2 y ntotal son numerarios; ab, ac1, ac2 y atotal son agregados.

Endpoints asociados:
- `/src/notas/asignaturas_pendientes_resumen_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/asignaturas_pendientes_resumen_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > resumen pendientes
- **Pills2:** ESTUDIOS > Preparación planes estudio > nº alum. por asignatura; vest > actas... > resumen pendientes
