---
id: "encargossacd.listas_exigencia_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar las exigencias de los centros"
capacidad: "encargossacd.listas_exigencia_ctr.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_exigencia_ctr"]
fragmentos: ["encargossacd.pantalla.listas_exigencia_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_exigencia_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Exigencia Ctr

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_exigencia_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar el listado de las exigencias de atención sacerdotal de cada centro o iglesia.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_exigencia_ctr`

## Escenarios Inferidos

### Listar las exigencias de los centros

Pasos propuestos:
1. Entrar en **listados**.
2. En «listados comunicaciones» elegir **exigencias ctr sv**, **exigencias ctr sf** o **exigencias igl, oc…**.
3. Se muestra, por centro o iglesia, quién es el encargado, rector o sacerdotes y las horas exigidas por franja, por ejemplo «(4 mañana, 2 tarde 1ª hora)».

Endpoints asociados:
- `/src/encargossacd/listas_exigencia_ctr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.ctr_igl`
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_exigencia_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

