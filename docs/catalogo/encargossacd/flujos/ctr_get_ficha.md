---
id: "encargossacd.ctr_get_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Abrir la ficha de un centro"
capacidad: "encargossacd.ctr_get_ficha.gestionar"
pantallas_principales: ["encargossacd.pantalla.ctr_get_ficha"]
fragmentos: ["encargossacd.pantalla.ctr_get_ficha"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Ctr Get Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.ctr_get_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Cargar la ficha de atención sacerdotal del centro elegido.

## Punto De Entrada

Menú **dre > Encargos > ficha ctr** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.ctr_get_ficha`

## Escenarios Inferidos

### Abrir la ficha de un centro

Pasos propuestos:
1. En **ficha ctr**, elegir el grupo y el centro.
2. La ficha aparece como «Atención ctr (nombre del centro)» con **sacd titular**, casilla **cl**, **dedicación** (mañanas, tarde 1ª hora, tarde 2ª hora), **Suplente**, colaboradores, **observaciones** y **exigencias del ctr**. En **Selección de sacd** las casillas **prelatura** y **sss+** limitan qué sacerdotes salen en las listas.
3. Para añadir un colaborador, pulsar **añadir sacd colaborador**; para fijar horas, **crear horario**.

Endpoints asociados:
- `/src/encargossacd/ctr_get_ficha_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.seleccion_sacd`
- `html.ok`
- `post.id_ubi`
- `post.seleccion_sacd`

Acciones JavaScript:
- `fnjs_cambiar_lista_sacd`
- `fnjs_cerrar`
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/ctr_get_ficha_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

