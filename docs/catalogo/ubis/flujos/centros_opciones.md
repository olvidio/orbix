---
id: "ubis.centros_opciones.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Elegir un centro en un desplegable"
capacidad: "ubis.centros_opciones.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/centros_opciones_data"]
estado_revision: "revisado"
---

# Flujo - Centros Opciones

## Objetivo De Usuario

Proporcionar la lista de centros que usan los desplegables de otras pantallas.

## Punto De Entrada

Sin entrada de menú propia: se abre desde la ficha del centro o casa.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Elegir un centro en un desplegable

Pasos propuestos:
1. En cualquier formulario con selector de centro, abrir el desplegable.
2. La lista se carga según el filtro de la pantalla; elegir uno.

Endpoints asociados:
- `/src/ubis/centros_opciones_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/centros_opciones_data`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
