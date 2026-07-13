---
id: "personas.persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Guardar o eliminar persona"
capacidad: "personas.persona.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_editar"]
acciones: ["crear_actualizar", "eliminar"]
endpoints: ["/src/personas/persona_update", "/src/personas/persona_eliminar"]
estado_revision: "revisado"
---

# Flujo - Guardar o eliminar persona

Persistencia de la ficha y borrado con restricción de delegación.

## Objetivo De Usuario

Guardar cambios en la ficha o eliminar un registro de la propia delegación.

## Punto De Entrada

- `personas_editar` + `_persona_form_js.phtml` (HashFront).

## Escenarios

### Guardar

1. Editar campos en la ficha (solo si `ok=1` por permiso oficina).
2. Pulsar guardar → `persona_update` con campos del Hash.
3. Confirmar mensaje de éxito o error del repositorio.

### Eliminar

1. Pulsar eliminar (PersonaEx admite botón extra).
2. Confirmar → `persona_eliminar`.
3. Solo procede si `persona.dl === mi_delef`.

## Endpoints Del Flujo

- `/src/personas/persona_update`
- `/src/personas/persona_eliminar`

## Errores Conocidos

- `No se ha pasado el id_nom`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha guardado` / `no se ha eliminado`

## Ruta de menú

- sin entrada de menú en el índice.
