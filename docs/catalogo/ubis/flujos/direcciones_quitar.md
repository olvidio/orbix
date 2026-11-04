---
id: "ubis.direcciones_quitar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Quitar una dirección del centro o casa"
capacidad: "ubis.direcciones_quitar.gestionar"
pantallas_principales: ["ubis.pantalla.direcciones_quitar"]
fragmentos: ["ubis.pantalla.direcciones_quitar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_quitar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Quitar

## Objetivo De Usuario

Quitar la dirección de un centro o casa sin borrarla.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_quitar`

## Escenarios Inferidos

### Quitar una dirección del centro o casa

Pasos propuestos:
1. En la ficha pulsar **dirección** en «Editar:».
2. Pulsar **Quitar Dirección**.
3. La dirección deja de aparecer en el centro o casa, pero sigue existiendo para otros.

Endpoints asociados:
- `/src/ubis/direcciones_quitar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_direccion`
- `post.id_ubi`
- `post.idx`
- `post.obj_dir`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/direcciones_quitar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
