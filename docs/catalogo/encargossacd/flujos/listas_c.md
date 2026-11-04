---
id: "encargossacd.listas_c.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Listar los encargos por sacerdote"
capacidad: "encargossacd.listas_c.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_c"]
fragmentos: ["encargossacd.pantalla.listas_c"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_c_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas C

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_c.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Sacar el listado de encargos por sacerdote (cr 1/14, 10,d).

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_c`

## Escenarios Inferidos

### Listar los encargos por sacerdote

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **por sacd**.
3. Se muestra cada sacerdote (agrupados por ciudad) con sus encargos y la dedicación entre paréntesis, por ejemplo «cl de: Raset (1 tarde 2ª hora)», «atención de: Ribalera (2 tarde 1ª hora)», estudio o descanso.

Endpoints asociados:
- `/src/encargossacd/listas_c_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_c_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

