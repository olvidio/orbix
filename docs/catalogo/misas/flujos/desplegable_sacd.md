---
id: "misas.desplegable_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Elegir sacerdote en una celda"
capacidad: "misas.desplegable_sacd.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_sacd"]
estado_revision: "revisado"
---

# Flujo - Desplegable sacd

## Objetivo De Usuario

Ofrecer, al pulsar una celda de la cuadrícula, los sacerdotes disponibles ese día.

## Punto De Entrada

Celda de la cuadrícula del plan (**Modificar plan**).

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Elegir sacerdote en una celda

Pasos propuestos:
1. En **Modificar plan**, pulsar la celda del día y encargo.
2. En **Selección de sacd** marcar qué sacerdotes se quieren ver: **libre a 1a hora**, **zona** (por defecto), **dl** o **de paso**; después elegir el **Sacerdote** de la lista, que solo muestra los disponibles ese día.
3. Indicar **Inicio**, **Fin** y **Observaciones** si procede y pulsar **Save**.

Endpoints asociados:
- `/src/misas/desplegable_sacd`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/desplegable_sacd`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
