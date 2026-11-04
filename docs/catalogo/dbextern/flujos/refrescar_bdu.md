---
id: "dbextern.refrescar_bdu.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Refrescar la copia de la BDU"
capacidad: "dbextern.refrescar_bdu.gestionar"
pantallas_principales: ["dbextern.pantalla.sincro_index"]
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/refrescar_bdu"]
estado_revision: "revisado"
---

# Flujo - Refrescar copia BDU

Actualiza la tabla temporal `tmp_bdu` desde la BDU externa.

## Objetivo De Usuario

Si los datos de las listas han cambiado después de la fecha que se muestra, actualizar la copia local de la BDU antes de sincronizar. Tarda varios minutos.

## Punto De Entrada

Enlace **refrescar** en la pantalla de sincronización («Actualizar datos desde BDU»).

## Escenarios Inferidos

### Refrescar la copia de la BDU

Pasos propuestos:
1. En la pantalla de sincronización, pulsar **refrescar**.
2. Esperar a que termine (varios minutos).
3. La pantalla se recarga con los contadores al día. Si falla, sale «Error al refrescar la BDU» con el motivo.

Endpoints asociados:
- `/src/dbextern/refrescar_bdu`

## Endpoints Del Flujo

- `/src/dbextern/refrescar_bdu`

## Errores Conocidos

- `Error al refrescar la BDU: …`

## Ruta de menú

- sin entrada de menú en el índice (acción embebida en `sincro_index`)
