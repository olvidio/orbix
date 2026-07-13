---
id: "personas.home_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Ver cabecera de persona"
capacidad: "personas.home_persona.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.home_persona"]
acciones: ["ver_resumen", "ir_ficha", "ir_dossiers"]
endpoints: ["/src/personas/home_persona_data"]
estado_revision: "revisado"
---

# Flujo - Ver cabecera de persona

Muestra el resumen de una persona y enlaces a ficha y dossiers.

## Objetivo De Usuario

Consultar datos básicos y acceder a la ficha completa o dossiers sin pasar por el listado.

## Punto De Entrada

- Clic en nombre del listado (`fnjs_home` o enlace HTML según preferencia `tabla_presentacion`).
- Navegación directa con `id_nom`, `id_tabla`, `obj_pau`.

## Escenarios

### Ver resumen

1. Seleccionar persona en listado o abrir enlace.
2. `home_persona` solicita `home_persona_data`.
3. Revisar datos y lista de dossiers embebida.

## Endpoints Del Flujo

- `/src/personas/home_persona_data`

## Errores Conocidos

- `No se encuentra la persona`
- Aviso: persona no válida

## Ruta de menú

- sin entrada de menú en el índice.
