---
id: "encargossacd.listas_com_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Preparar la comunicación a los sacerdotes"
capacidad: "encargossacd.listas_com_sacd.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_com_sacd"]
fragmentos: ["encargossacd.pantalla.listas_com_sacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Com Sacd

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_sacd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Preparar el escrito que se envía a cada sacerdote con sus encargos.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_sacd`

## Escenarios Inferidos

### Preparar la comunicación a los sacerdotes

Pasos propuestos:
1. Entrar en **listados**.
2. Elegir **comunicación a sacd Prelatura** o **comunicación a sacd sss+**.
3. Aparece un escrito por sacerdote (en el idioma configurado, por ejemplo catalán) con la relación de sus labores y la dedicación prevista (marcando si es **coordinador**). Es una salida muy larga: un escrito por cada sacerdote.

Endpoints asociados:
- `/src/encargossacd/listas_com_sacd_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/listas_com_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

