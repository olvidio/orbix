---
id: "ubis.direcciones_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Tabla"
capacidad: "ubis.direcciones_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_tabla"]
estado_revision: "revisado"
---

# Flujo - Direcciones Tabla

## Objetivo De Usuario

Busca direcciones por cp/ciudad/país y muestra tabla para asignar al ubi.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_tabla`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.c_p`
- `post.ciudad`
- `post.id_ubi`
- `post.obj_dir`
- `post.pais`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/direcciones_tabla`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
