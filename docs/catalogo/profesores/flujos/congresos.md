---
id: "profesores.congresos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Consultar la asistencia a congresos"
capacidad: "profesores.congresos.gestionar"
pantallas_principales: ["profesores.pantalla.congresos"]
fragmentos: []
acciones: ["consultar"]
endpoints: ["/src/profesores/congresos"]
estado_revision: "revisado"
---

# Flujo - Consultar congresos

Consulta global de asistencia a congresos del claustro STGR.

## Objetivo De Usuario

Consultar la asistencia de los profesores a congresos, claustros, reuniones y otros eventos.

## Punto De Entrada

Menú **vest > actas... > asistencia a congresos**.

## Escenarios Inferidos

### Consultar la asistencia a congresos

Pasos propuestos:
1. Entrar en **asitencia a congresos**.
2. Se muestra una tabla con «apellidos, nombre», «tipo» (claustro, reunión, congreso, cv…), «lugar», «inicio», «fin» y «organiza».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

Endpoints asociados:
- `/src/profesores/congresos`

## Endpoints Del Flujo

- `/src/profesores/congresos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > asitencia a congresos
- **Pills2:** sin entrada en el índice
