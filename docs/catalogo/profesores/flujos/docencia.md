---
id: "profesores.docencia.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Consultar la docencia"
capacidad: "profesores.docencia.gestionar"
pantallas_principales: ["profesores.pantalla.docencia"]
fragmentos: []
acciones: ["consultar"]
endpoints: ["/src/profesores/docencia"]
estado_revision: "revisado"
---

# Flujo - Ver docencia global

Consulta del registro histórico de docencia STGR de todo el claustro.

## Objetivo De Usuario

Consultar la docencia impartida por los profesores: curso, asignatura, modo y acta.

## Punto De Entrada

Menú **vest > actas... > ver docencia**.

## Escenarios Inferidos

### Consultar la docencia

Pasos propuestos:
1. Entrar en **ver docencia**.
2. Se muestra una tabla con «apellidos, nombre», «inicio curso», «asignatura», «modo» (por ejemplo «sem. invierno» o «ca/cv») y «acta».
3. Arriba a la derecha de la tabla hay dos iconos (lupa y guardar) para buscar en ella y exportarla.

Endpoints asociados:
- `/src/profesores/docencia`

## Endpoints Del Flujo

- `/src/profesores/docencia`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > ver docencia
- **Pills2:** sin entrada en el índice
