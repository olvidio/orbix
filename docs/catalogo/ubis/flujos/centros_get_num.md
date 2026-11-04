---
id: "ubis.centros_get_num.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Consultar o cambiar buzón, pi y cartas"
capacidad: "ubis.centros_get_num.gestionar"
pantallas_principales: ["ubis.pantalla.centros_get_num"]
fragmentos: ["ubis.pantalla.centros_get_num"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_get_num"]
estado_revision: "revisado"
---

# Flujo - Centros Get Num

## Objetivo De Usuario

Listar los centros de la dl con su nº de buzón, ejemplares de pi y cartas, para editarlos.

## Punto De Entrada

Menú **scdl > direcciones > modificar centros** (Pills2: CASAS Y CTR > direcciones > modificar centros).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_get_num`

## Escenarios Inferidos

### Consultar o cambiar buzón, pi y cartas

Pasos propuestos:
1. Entrar en **modificar centros** y elegir **pi, cartas, nº buzón**; pulsar **buscar**.
2. Se muestra la lista con el nº de buzón, nº de ejemplares de pi y nº de cartas.
3. Pulsar un centro para editar sus números.

Endpoints asociados:
- `/src/ubis/centros_get_num`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/centros_get_num`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** scdl > direcciones > modificar centros
- **Pills2:** scdl > direcciones > modificar centros
