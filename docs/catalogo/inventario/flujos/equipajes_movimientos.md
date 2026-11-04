---
id: "inventario.equipajes_movimientos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Consultar los movimientos de un equipaje"
capacidad: "inventario.equipajes_movimientos.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_movimientos"]
fragmentos: ["inventario.pantalla.equipajes_movimientos"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/equipajes_movimientos"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Movimientos

## Objetivo De Usuario

Ver qué documentos se han añadido o quitado de las valijas de un equipaje.

## Punto De Entrada

Menú **Inventario > equipajes > movimientos maletas**, tras elegir el equipaje.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_movimientos`

## Escenarios Inferidos

### Consultar los movimientos de un equipaje

Pasos propuestos:
1. Abrir **movimientos maletas**.
2. Marcar el equipaje y pulsar **seleccionar**.
3. Para cada valija se listan los documentos **añadir** y **quitar**.

Endpoints asociados:
- `/src/inventario/equipajes_movimientos`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_movimientos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > movimientos maletas
- **Pills2:** —
