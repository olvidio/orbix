---
id: "actividades.actividad_select_ubi_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Desplegables lugar (popup)"
capacidad: "actividades.actividad_select_ubi_desplegable.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable"]
estado_revision: "revisado"
---

# Flujo - Desplegables lugar (popup)

Opciones de historial frecuente y regiones en el popup seleccionar lugar.

## Objetivo De Usuario

Al elegir modo historial o región, cargar casas/ubis candidatas antes de confirmar.

## Punto De Entrada

`actividad_select_ubi.phtml` → `fnjs_cargar_desplegable` / `fnjs_construir_desplegable`.

## Escenarios

### Ejecutar

1. Usuario elige tipo de búsqueda (`freq`, `region`, …) y `dl_org`/`isfsv`.
2. AJAX a `actividad_select_ubi_desplegable`.
3. Pinta desplegable de ubis; al elegir, rellena campos del opener.

## Endpoints Del Flujo

- `/src/actividades/actividad_select_ubi_desplegable`

## Errores Conocidos

- `opción no definida: tipo=…`
- `falta saber quien organiza` (modo freq sin `dl_org`)

## Ruta de menú

sin entrada de menú en el índice (popup desde ficha/planning).
