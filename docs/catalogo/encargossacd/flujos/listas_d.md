---
id: "encargossacd.listas_d.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar la tabla por sacerdote"
capacidad: "encargossacd.listas_d.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_d"]
fragmentos: ["encargossacd.pantalla.listas_d"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_d_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas D

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_d.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar la tabla por sacerdote (cr 9/20, 10), para sv o sf.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_d`

## Escenarios Inferidos

### Listar la tabla por sacerdote

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **tabla por sacd** o **tabla por sacd sf**.
3. Se muestra, por sacerdote y ciudad, la lista de centros con el nº de horas (por ejemplo Raset 3, Moneders 5), incluidos estudio y descanso.

Endpoints asociados:
- `/src/encargossacd/listas_d_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_d_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

