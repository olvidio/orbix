---
id: "ubis.centros_get_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar o cambiar plazas y sede"
capacidad: "ubis.centros_get_plazas.gestionar"
pantallas_principales: ["ubis.pantalla.centros_get_plazas"]
fragmentos: ["ubis.pantalla.centros_get_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_get_plazas"]
estado_revision: "revisado"
---

# Flujo - Centros Get Plazas

## Objetivo De Usuario

Listar los centros de la dl con plazas, habitaciones individuales y si son sede, para editarlos.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_get_plazas`

## Escenarios Inferidos

### Consultar o cambiar plazas y sede

Pasos propuestos:
1. Entrar en **modificar centros** y elegir **sede, plazas**; pulsar **buscar**.
2. Se muestra la lista con plazas, habitaciones individuales y la marca de sede.
3. Pulsar un centro para editarlo.

Endpoints asociados:
- `/src/ubis/centros_get_plazas`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/centros_get_plazas`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
