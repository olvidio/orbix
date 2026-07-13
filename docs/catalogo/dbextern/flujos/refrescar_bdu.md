---
id: "dbextern.refrescar_bdu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Refrescar copia BDU"
capacidad: "dbextern.refrescar_bdu.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/refrescar_bdu"]
estado_revision: "revisado"
---

# Flujo - Refrescar copia BDU

Actualiza la tabla temporal `tmp_bdu` desde la BDU externa.

## Objetivo De Usuario

Si los datos de listas cambiaron después de la fecha mostrada, refrescar la copia local antes de
sincronizar (operación de varios minutos).

## Punto De Entrada

Enlace **refrescar** en `sincro_index` (`fnjs_refrescar`).

## Escenarios

### Ejecutar

1. Usuario pulsa **refrescar**.
2. AJAX a `refrescar_bdu` con HashFront (`que=algo`).
3. Tras éxito, recarga `sincro_index` en `#main`.

## Endpoints Del Flujo

- `/src/dbextern/refrescar_bdu`

## Errores Conocidos

- `Error al refrescar la BDU: …`

## Ruta de menú

- sin entrada de menú en el índice (acción embebida en `sincro_index`)
