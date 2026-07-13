---
id: "inventario.inventario_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Inventario Ctr"
capacidad: "inventario.inventario_ctr.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.doc_imprimir_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/inventario_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Inventario Ctr

Propuesta generada automaticamente desde la capacidad `inventario.inventario_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Impresión inventario de centros: selección en `doc_de_ctr`, render en `doc_imprimir_ctr` vía `inventario_ctr`.

## Punto De Entrada

- `inventario.pantalla.inventario_que → doc_de_ctr`



## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_ctr`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.sel`

Acciones JavaScript:
- `fnjs_ver_equipaje`

## Endpoints Del Flujo

- `/src/inventario/inventario_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —
