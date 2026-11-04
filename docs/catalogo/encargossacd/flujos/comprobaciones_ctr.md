---
id: "encargossacd.comprobaciones_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Eliminar encargos de centros inexistentes"
capacidad: "encargossacd.comprobaciones_ctr.gestionar"
pantallas_principales: ["encargossacd.pantalla.comprobaciones"]
fragmentos: ["encargossacd.pantalla.comprobaciones"]
acciones: ["ejecutar"]
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
estado_revision: "revisado"
---

# Flujo - Gestionar Comprobaciones Ctr

Propuesta generada automaticamente desde la capacidad `encargossacd.comprobaciones_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Limpiar los encargos ligados a centros que ya no existen.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.comprobaciones`

## Escenarios Inferidos

### Eliminar encargos de centros inexistentes

Pasos propuestos:
1. Entrar en **listados**.
2. En **comprobaciones** pulsar **Eliminar encargos de centros inexistentes**.
3. Se borran los encargos de centros inactivos y los que no tienen sacerdote.

Endpoints asociados:
- `/src/encargossacd/comprobaciones_ctr`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/comprobaciones_ctr`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

