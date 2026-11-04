---
id: "ubis.centros_get_labor.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar o cambiar el tipo de labor de los centros"
capacidad: "ubis.centros_get_labor.gestionar"
pantallas_principales: ["ubis.pantalla.centros_get_labor"]
fragmentos: ["ubis.pantalla.centros_get_labor"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_get_labor"]
estado_revision: "revisado"
---

# Flujo - Centros Get Labor

## Objetivo De Usuario

Listar los centros de la dl con su tipo de centro y tipo de labor, para editarlos.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_get_labor`

## Escenarios Inferidos

### Consultar o cambiar el tipo de labor de los centros

Pasos propuestos:
1. Entrar en **modificar centros** y elegir **labor** en el desplegable; pulsar **buscar**.
2. Se muestra la lista de centros con su tipo de centro y de labor.
3. Pulsar un centro para abrir el formulario de edición.

Endpoints asociados:
- `/src/ubis/centros_get_labor`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_modificar`

## Endpoints Del Flujo

- `/src/ubis/centros_get_labor`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
