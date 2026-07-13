---
id: "personas.personas_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Abrir ficha de persona"
capacidad: "personas.personas_editar.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_editar"]
acciones: ["obtener_datos", "alta"]
endpoints: ["/src/personas/personas_editar_data"]
estado_revision: "revisado"
---

# Flujo - Abrir ficha de persona

Carga del formulario de alta o edición según colectivo y permisos.

## Objetivo De Usuario

Crear una persona nueva o editar la ficha existente con los campos del colectivo correspondiente.

## Punto De Entrada

- Listado: botón «ficha» (edición) o enlace alta (`nuevo=1`, `apellido1` precargado).
- Cabecera: enlace «ficha».

## Escenarios

### Alta

1. Desde listado con permiso `3`, pulsar alta.
2. `personas_editar_data` con `nuevo=1` asigna `id_nom` y defaults.
3. Completar formulario y guardar (flujo `persona`).

### Edición

1. Seleccionar persona y abrir ficha.
2. Cargar datos vía `sel` o `id_nom`.
3. Plantilla según `obj_pau` y permiso oficina.

## Endpoints Del Flujo

- `/src/personas/personas_editar_data`

## Errores Conocidos

- `No se ha pasado el id_nom`
- `No se encuentra la persona`

## Ruta de menú

- sin entrada de menú en el índice.
