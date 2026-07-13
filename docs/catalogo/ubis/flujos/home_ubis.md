---
id: "ubis.home_ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Home Ubis"
capacidad: "ubis.home_ubis.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.home_ubis"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/home_ubis_data"]
estado_revision: "revisado"
---

# Flujo - Home Ubis

## Objetivo De Usuario

Construye la ficha resumen de un ubi con dirección, telecomunicaciones y objetos pau/dir.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.home_ubis`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.bloque`
- `post.id_ubi`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_left_side_show`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/home_ubis_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
