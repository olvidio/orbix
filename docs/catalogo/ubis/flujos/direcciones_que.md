---
id: "ubis.direcciones_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Buscar una dirección existente"
capacidad: "ubis.direcciones_que.gestionar"
pantallas_principales: ["ubis.pantalla.direcciones_que"]
fragmentos: ["ubis.pantalla.direcciones_que"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_que"]
estado_revision: "revisado"
---

# Flujo - Direcciones Que

## Objetivo De Usuario

Buscar una dirección que ya existe para asignarla a un centro o casa.

## Punto De Entrada

Ficha del centro o casa, apartado **dirección**, botón **añadir dirección**.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_que`

## Escenarios Inferidos

### Buscar una dirección existente

Pasos propuestos:
1. En la ficha, pulsar **añadir dirección**.
2. Escribir código postal, población y/o país de la dirección.
3. Pulsar buscar para ver la tabla de direcciones.

Endpoints asociados:
- `/src/ubis/direcciones_que`

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
