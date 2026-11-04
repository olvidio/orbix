---
id: "encargossacd.listas_b.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar la atención de iglesias, oc y lp"
capacidad: "encargossacd.listas_b.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_b"]
fragmentos: ["encargossacd.pantalla.listas_b"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_b_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas B

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_b.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar el listado de atención de iglesias, obras corporativas y lugares de paso (cr 1/14, 10,b).

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_b`

## Escenarios Inferidos

### Listar la atención de iglesias, oc y lp

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **atención iglesias, oc y lp** (o **oc y lp sf**).
3. Se muestra el listado.

Endpoints asociados:
- `/src/encargossacd/listas_b_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sf`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_b_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

