---
id: "encargossacd.listas_cl.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar los centros de la sss+"
capacidad: "encargossacd.listas_cl.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_cl"]
fragmentos: ["encargossacd.pantalla.listas_cl"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_cl_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Cl

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_cl.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar el listado de los centros de la sss+ (cr 1/14, 10,c).

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_cl`

## Escenarios Inferidos

### Listar los centros de la sss+

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **cl sss+**.
3. Se muestra el listado.

Endpoints asociados:
- `/src/encargossacd/listas_cl_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_cl_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

