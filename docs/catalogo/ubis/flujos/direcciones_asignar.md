---
id: "ubis.direcciones_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Asignar"
capacidad: "ubis.direcciones_asignar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_asignar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_asignar"]
estado_revision: "revisado"
---

# Flujo - Direcciones Asignar

## Objetivo De Usuario

Asocia una dirección existente a un ubi sin marcarla como propietaria.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_asignar`

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
