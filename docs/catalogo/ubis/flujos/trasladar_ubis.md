---
id: "ubis.trasladar_ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Trasladar Ubis"
capacidad: "ubis.trasladar_ubis.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.trasladar_ubis"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/trasladar_ubis"]
estado_revision: "revisado"
---

# Flujo - Trasladar Ubis

## Objetivo De Usuario

Traslada centros y casas seleccionados a otra delegación destino.

## Punto De Entrada

Menú Legacy: scdl > direcciones > listados. Pills2: Calendario > centros y casas > listados.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.trasladar_ubis`

## Escenarios Inferidos

### Ejecutar

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

- `/src/ubis/trasladar_ubis`

## Errores Conocidos

- `No se han seleccionado ubis.`

## Ruta de menú

- **Legacy:** scdl > direcciones > listados
- **Pills2:** Calendario > centros y casas > listados
