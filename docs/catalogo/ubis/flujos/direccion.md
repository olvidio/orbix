---
id: "ubis.direccion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Guardar una dirección"
capacidad: "ubis.direccion.gestionar"
pantallas_principales: ["ubis.pantalla.direccion_update"]
fragmentos: ["ubis.pantalla.direccion_update"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/direccion_update"]
estado_revision: "revisado"
---

# Flujo - Direccion

## Objetivo De Usuario

Guardar los datos de la dirección de un centro o casa.

## Punto De Entrada

Menú **scdl > direcciones > buscar**: buscar, pulsar el nombre del centro o casa en la tabla y usar «Editar: **centro** / **dirección** / **telecos**» de la ficha.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direccion_update`

## Escenarios Inferidos

### Guardar una dirección

Pasos propuestos:
1. En la ficha pulsar **dirección** en «Editar:».
2. Rellenar **Nombre sede**, **Dirección**, **Dirección postal**, **Código postal**, **Población**, **Provincia**, **País**, **Comentarios dirección**, **Fecha introducción de datos**, **Latitud** y **Longitud**; marcar **Propietario** o **Principal** si procede (hay un enlace **ver ejemplo dirección**).
3. Pulsar **Guardar Cambios**.
4. Con **Añadir Dirección** se asigna otra dirección al centro y con **Quitar Dirección** se le quita la actual.

Endpoints asociados:
- `/src/ubis/direccion_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/direccion_update`

## Errores Conocidos

- `no se encuentra el ubi`
- `operación no soportada para este tipo de dirección`
- `no se encuentra la dirección`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
