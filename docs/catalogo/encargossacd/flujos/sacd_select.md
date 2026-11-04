---
id: "encargossacd.sacd_select.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Elegir un sacerdote"
capacidad: "encargossacd.sacd_select.gestionar"
pantallas_principales: ["encargossacd.pantalla.sacd_ficha_ajax"]
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_select_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Select

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_select.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Rellenar el desplegable de sacerdotes según el tipo elegido.

## Punto De Entrada

Menú **dre > Encargos > ficha sacd** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha sacd).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ficha_ajax`

## Escenarios Inferidos

### Elegir un sacerdote

Pasos propuestos:
1. En **ficha sacd** o **ausencias**, elegir el **tipo**.
2. El desplegable **sacd** se rellena con los sacerdotes de ese tipo; elegir uno.

Endpoints asociados:
- `/src/encargossacd/sacd_select_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `form.enc_num`
- `form.id_tipo_enc`
- `form.mas`
- `form.observ`
- `html.dedic_m[<?= $j ?>]`
- `html.dedic_t[<?= $j ?>]`
- `html.dedic_v[<?= $j ?>]`
- `html.enc_num`
- `html.ok`
- `post.filtro_sacd`
- `post.id_nom`
- `post.que`

Acciones JavaScript:
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_mas_enc`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_select_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

