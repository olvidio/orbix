---
id: "personas.persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Guardar los datos personales"
capacidad: "personas.persona.gestionar"
pantallas_principales: ["personas.pantalla.personas_editar"]
fragmentos: ["personas.pantalla.personas_editar"]
acciones: ["crear_actualizar", "eliminar"]
endpoints: ["/src/personas/persona_update", "/src/personas/persona_eliminar"]
estado_revision: "revisado"
---

# Flujo - Guardar o eliminar persona

Persistencia de la ficha y borrado con restricción de delegación.

## Objetivo De Usuario

Guardar o eliminar los datos personales de una persona.

## Punto De Entrada

Menú **vest > buscar persona** (por ejemplo **n r/dl**, **n de paso**, **agd r/dl**; también en vsm, vsg y scdl). Ficha > **editar**.

## Escenarios Inferidos

### Guardar los datos personales

Pasos propuestos:
1. En «DATOS PERSONALES» cambiar los campos necesarios.
2. Pulsar **Guardar Cambios** (solo si tu rol lo permite).
3. Si hay un problema el servidor lo avisa con un mensaje.

Endpoints asociados:
- `/src/personas/persona_update`
- `/src/personas/persona_eliminar`

## Endpoints Del Flujo

- `/src/personas/persona_update`
- `/src/personas/persona_eliminar`

## Errores Conocidos

- `No se ha pasado el id_nom`
- `No se ha eliminado, porque no es de mi dl`
- `hay un error, no se ha guardado` / `no se ha eliminado`

## Ruta de menú

- sin entrada de menú en el índice.
