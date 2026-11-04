---
id: "ubis.direcciones_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Asignar una dirección existente"
capacidad: "ubis.direcciones_asignar.gestionar"
pantallas_principales: ["ubis.pantalla.direcciones_asignar"]
fragmentos: ["ubis.pantalla.direcciones_asignar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_asignar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Asignar

## Objetivo De Usuario

Asociar una dirección ya existente a un centro o casa.

## Punto De Entrada

Tabla de direcciones, tras buscar.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_asignar`

## Escenarios Inferidos

### Asignar una dirección existente

Pasos propuestos:
1. Buscar la dirección y marcarla en la tabla.
2. Pulsar asignar.
3. La dirección aparece en la ficha del centro o casa; no se marca como propietaria.

Endpoints asociados:
- `/src/ubis/direcciones_asignar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_direccion`
- `post.id_ubi`
- `post.obj_dir`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/direcciones_asignar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
