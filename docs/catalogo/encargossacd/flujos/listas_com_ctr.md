---
id: "encargossacd.listas_com_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Preparar la comunicación a los centros"
capacidad: "encargossacd.listas_com_ctr.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_com_ctr"]
fragmentos: ["encargossacd.pantalla.listas_com_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Com Ctr

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Preparar el escrito que se envía a cada centro con su sacerdote titular, suplente y colaboradores.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_ctr`

## Escenarios Inferidos

### Preparar la comunicación a los centros

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **comunicación a los centros sv** (o **sf**).
3. Aparece un escrito por centro con **titular**, **suplente** y **colaboradores**, en el idioma del centro, con cabecera y lugar/fecha (por ejemplo «Barcelona, 3.11.2026»). El curso que aparece en el texto («para el curso …») se calcula solo, según la fecha de hoy (el curso empieza en septiembre); el resto del texto se cambia en **modificar los textos de las comunicaciones**.

Endpoints asociados:
- `/src/encargossacd/listas_com_ctr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sfsv`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_com_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

