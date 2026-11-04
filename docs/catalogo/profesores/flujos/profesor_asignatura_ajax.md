---
id: "profesores.profesor_asignatura_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Ver los profesores de una asignatura"
capacidad: "profesores.profesor_asignatura_ajax.gestionar"
pantallas_principales: ["profesores.pantalla.profesor_asignatura_ajax"]
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
acciones: ["consultar"]
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
estado_revision: "revisado"
---

# Flujo - Tabla AJAX profesores asignatura

Fragmento que completa el flujo **profesor para asignatura**.

## Objetivo De Usuario

Cargar la lista de profesores de la asignatura elegida.

## Punto De Entrada

Menú **vest > buscar persona > profesor para asignatura**.

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.profesor_asignatura_ajax`

## Escenarios Inferidos

### Ver los profesores de una asignatura

Pasos propuestos:
1. Elegir la asignatura en el desplegable.
2. La lista de profesores se actualiza en la misma pantalla.

Endpoints asociados:
- `/src/profesores/profesor_asignatura_ajax`

## Endpoints Del Flujo

- `/src/profesores/profesor_asignatura_ajax`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

sin entrada de menú en el índice (fragmento AJAX de `profesor_asignatura_que`)
