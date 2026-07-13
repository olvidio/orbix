---
id: "ubis.teleco.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco"
capacidad: "ubis.teleco.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/teleco_eliminar", "/src/ubis/teleco_guardar"]
estado_revision: "revisado"
---

# Flujo - Teleco

## Objetivo De Usuario

Elimina una o más telecomunicaciones del ubi por claves primarias codificadas.

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/ubis/teleco_eliminar`

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
