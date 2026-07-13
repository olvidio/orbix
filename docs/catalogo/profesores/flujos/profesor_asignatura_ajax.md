---
id: "profesores.profesor_asignatura_ajax.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Tabla AJAX profesores asignatura"
capacidad: "profesores.profesor_asignatura_ajax.gestionar"
pantallas_principales: []
fragmentos: ["profesores.pantalla.profesor_asignatura_ajax"]
acciones: ["consultar"]
endpoints: ["/src/profesores/profesor_asignatura_ajax"]
estado_revision: "revisado"
---

# Flujo - Tabla AJAX profesores asignatura

Fragmento que completa el flujo **profesor para asignatura**.

## Objetivo De Usuario

Obtener la lista de profesores para la asignatura seleccionada sin recargar la pantalla principal.

## Punto De Entrada

Invocado por `fnjs_profes` desde `profesor_asignatura_que` al cambiar `id_asignatura`.

## Fragmentos O Pantallas Auxiliares

- `profesores.pantalla.profesor_asignatura_ajax`

## Escenarios Inferidos

### Consultar

Pasos:
1. El usuario cambia la asignatura en el desplegable.
2. POST a `profesor_asignatura_ajax.php` con `id_asignatura`.
3. Se inserta HTML de tabla en el contenedor de la pantalla padre.

Endpoints asociados:
- `/src/profesores/profesor_asignatura_ajax`

## Endpoints Del Flujo

- `/src/profesores/profesor_asignatura_ajax`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

sin entrada de menú en el índice (fragmento AJAX de `profesor_asignatura_que`)
