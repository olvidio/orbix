---
id: "ubis.teleco_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Editar"
capacidad: "ubis.teleco_editar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_editar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_editar"]
estado_revision: "revisado"
---

# Flujo - Teleco Editar

## Objetivo De Usuario

Carga el formulario de alta/edición de una telecomunicación de un ubi.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_editar`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_desc_teleco`
- `form.id_tipo_teleco`
- `form.mod`
- `form.num_teleco`
- `form.observ`
- `html.mod`
- `html.num_teleco`
- `html.observ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.s_pkey`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_descripcion`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/teleco_editar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
