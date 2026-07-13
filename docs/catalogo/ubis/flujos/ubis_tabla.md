---
id: "ubis.ubis_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Tabla"
capacidad: "ubis.ubis_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_tabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_tabla_data"]
estado_revision: "revisado"
---

# Flujo - Ubis Tabla

## Objetivo De Usuario

Busca ubis por nombre y/o dirección con filtros tipo/loc y construye tabla navegable.

## Punto De Entrada

Menú Legacy: scdl > direcciones > buscar. Pills2: scdl > direcciones > buscar.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_tabla`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `html.b_mas`
- `post.stack`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/ubis_tabla_data`

## Errores Conocidos

- `debe poner algún criterio de búsqueda`

## Ruta de menú

- **Legacy:** scdl > direcciones > buscar
- **Pills2:** scdl > direcciones > buscar
