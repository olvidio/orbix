---
id: "ubis.trasladar_ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Trasladar centros o casas"
capacidad: "ubis.trasladar_ubis.gestionar"
pantallas_principales: ["ubis.pantalla.trasladar_ubis"]
fragmentos: ["ubis.pantalla.trasladar_ubis"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/trasladar_ubis"]
estado_revision: "revisado"
---

# Flujo - Trasladar Ubis

## Objetivo De Usuario

Pasar los centros o casas seleccionados a otra delegación.

## Punto De Entrada

Menú **scdl > direcciones > listados** (también Calendario o dre > centros y casas > listados).

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.trasladar_ubis`

## Escenarios Inferidos

### Trasladar centros o casas

Pasos propuestos:
1. En **listados**, marcar uno o varios centros o casas.
2. Elegir la delegación en **Trasladar a** y pulsar **Trasladar**.
3. Comprobar el listado: los trasladados ya no pertenecen a la delegación de origen.

Endpoints asociados:
- `/src/ubis/trasladar_ubis`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/trasladar_ubis`

## Errores Conocidos

- `No se han seleccionado ubis.`

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
