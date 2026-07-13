---
id: "ubis.ubis_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Buscar"
capacidad: "ubis.ubis_buscar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_buscar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_buscar_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Buscar

## Objetivo De Usuario

Devuelve opciones de desplegables para el formulario de búsqueda de ubis.

## Punto De Entrada

Menú Legacy: scdl > direcciones > buscar. Pills2: scdl > direcciones > buscar.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_buscar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.b_buscar`
- `html.b_mas`
- `html.cmb`
- `html.labor[]`
- `html.loc`
- `html.ok`
- `html.opcion`
- `html.select[]`
- `html.simple`
- `html.tipo`
- `post.loc`
- `post.simple`
- `post.tipo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_update_div`
- `fnjs_ver_solo`

## Endpoints Del Flujo

- `/src/ubis/ubis_buscar_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar
