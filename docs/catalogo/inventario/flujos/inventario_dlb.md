---
id: "inventario.inventario_dlb.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Inventario Dlb"
capacidad: "inventario.inventario_dlb.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.doc_imprimir_dlb"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/inventario_dlb"]
estado_revision: "revisado"
---

# Flujo - Gestionar Inventario Dlb

Propuesta generada automaticamente desde la capacidad `inventario.inventario_dlb.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Impresión inventario DLB/casa vía `inventario_dlb`.

## Punto De Entrada

- `inventario.pantalla.doc_de_dlb`



## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.doc_imprimir_dlb`

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

- `/src/inventario/inventario_dlb`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > inventarios > de centros o dlb
- **Pills2:** —
