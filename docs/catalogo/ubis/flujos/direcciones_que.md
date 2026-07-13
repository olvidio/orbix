---
id: "ubis.direcciones_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Que"
capacidad: "ubis.direcciones_que.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_que"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_que"]
estado_revision: "revisado"
---

# Flujo - Direcciones Que

## Objetivo De Usuario

Prepara el formulario de búsqueda de direcciones existentes para asignar a un ubi.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_que`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.c_p`
- `form.ciudad`
- `form.id_ubi`
- `form.obj_dir`
- `form.pais`
- `html.btn_ok`
- `post.id_ubi`
- `post.obj_dir`

Acciones JavaScript:
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/ubis/direcciones_que`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
