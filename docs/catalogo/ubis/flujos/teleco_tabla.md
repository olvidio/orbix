---
id: "ubis.teleco_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Tabla"
capacidad: "ubis.teleco_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_tabla"]
estado_revision: "revisado"
---

# Flujo - Teleco Tabla

## Objetivo De Usuario

Lista las telecomunicaciones de un centro o casa con botones según permisos.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_tabla`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.mod`
- `form.sel`
- `html.btn_new`
- `html.mod`
- `html.refresh`
- `post.id_ubi`
- `post.obj_pau`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/ubis/teleco_tabla`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
