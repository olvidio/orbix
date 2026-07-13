---
id: "ubis.direcciones_quitar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Quitar"
capacidad: "ubis.direcciones_quitar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_quitar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_quitar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Quitar

## Objetivo De Usuario

Desvincula una dirección del ubi según el índice en la lista CSV de ids.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_quitar`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
