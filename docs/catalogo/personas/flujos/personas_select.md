---
id: "personas.personas_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Buscar y listar personas"
capacidad: "personas.personas_select.gestionar"
pantallas_principales: ["personas.pantalla.personas_que", "personas.pantalla.personas_select"]
fragmentos: []
acciones: ["buscar", "seleccionar", "acciones_listado"]
endpoints: ["/src/personas/personas_select_data"]
estado_revision: "revisado"
---

# Flujo - Buscar y listar personas

Búsqueda por criterios y visualización del listado con acciones sobre la selección.

## Objetivo De Usuario

Encontrar personas del colectivo indicado por el menú, revisar resultados y lanzar acciones
(ficha, dossiers, STGR, traslado, módulos satélite).

## Punto De Entrada

- `personas_que.php` (criterios) → `personas_select.php` (tabla).

## Escenarios

### Buscar

1. Abrir entrada de menú (define `tabla`, `na`, `tipo`, `es_sacd`).
2. Rellenar criterios opcionales en `personas_que`.
3. Enviar → `personas_select` llama a `personas_select_data`.
4. Revisar total y avisos de región STGR si aparecen.

### Acciones sobre selección

1. Marcar fila(s) en la tabla (`sel=id_nom#id_tabla`).
2. Pulsar botón contextual (ficha, dossiers, modificar stgr, etc.).

## Endpoints Del Flujo

- `/src/personas/personas_select_data`

## Errores Conocidos

- `No se encuentra ningún centro con esta condición`
- Avisos suaves región/persona no válida (listado vacío con mensaje)

## Ruta de menú

Herencia de `personas_que` — ver variantes vsm/PERSONAS en `_referencia_menus.md`
(p. ej. `vsm > buscar n > n r/dl` / `PERSONAS > Numerarios > Buscar n de la r/dl`).
