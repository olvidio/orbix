---
id: "ubis.teleco.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Eliminar una telecomunicación"
capacidad: "ubis.teleco.gestionar"
pantallas_principales: ["ubis.pantalla.teleco_editar", "ubis.pantalla.teleco_tabla"]
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/teleco_eliminar", "/src/ubis/teleco_guardar"]
estado_revision: "revisado"
---

# Flujo - Teleco

## Objetivo De Usuario

Eliminar una telecomunicación de un centro o casa.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar una telecomunicación

Pasos propuestos:
1. En **telecos**, marcar el registro y pulsar **eliminar**.
2. Confirmar el aviso «¿Está seguro que desea eliminar este registro?».

Endpoints asociados:
- `/src/ubis/teleco_eliminar`
- `/src/ubis/teleco_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/teleco_eliminar`
- `/src/ubis/teleco_guardar`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
