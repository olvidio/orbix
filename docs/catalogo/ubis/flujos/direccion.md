---
id: "ubis.direccion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direccion"
capacidad: "ubis.direccion.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direccion_update"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/direccion_update"]
estado_revision: "revisado"
---

# Flujo - Direccion

## Objetivo De Usuario

Crea o modifica una dirección y su relación con el ubi (principal, propietario).

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direccion_update`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

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
