---
id: "dbextern.sincro_syncro.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Sincronizar fichas unidas"
capacidad: "dbextern.sincro_syncro.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_syncro"]
estado_revision: "revisado"
---

# Flujo - Sincronizar fichas unidas

Ejecuta la sincronización masiva del punto 1.

## Objetivo De Usuario

Actualizar en Aquinate los datos de todas las personas ya vinculadas a la BDU en la DL actual.

## Punto De Entrada

Enlace **ejecutar** del punto 1 en `sincro_index` (`fnjs_sincronizar`).

## Escenarios

### Ejecutar

1. Envía `region`, `dl_listas`, `tipo_persona` con HashFront.
2. `SincroPersonas` sincroniza cada persona con `id_match`.
3. Muestra `alert` con mensaje resumen o errores parciales.

## Endpoints Del Flujo

- `/src/dbextern/sincro_syncro`

## Errores Conocidos

- Mensajes de syncro por persona (dentro de `mensaje`, no siempre `success: false`)

## Ruta de menú

- sin entrada de menú en el índice (acción embebida en `sincro_index`)
