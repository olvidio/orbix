---
id: "encargossacd.listas_com_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Modificar los textos de las comunicaciones"
capacidad: "encargossacd.listas_com_txt.gestionar"
pantallas_principales: ["encargossacd.pantalla.listas_com_txt"]
fragmentos: ["encargossacd.pantalla.listas_com_txt", "encargossacd.pantalla.listas_com_txt_get", "encargossacd.pantalla.listas_com_txt_update"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Com Txt

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_txt.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Modificar los textos fijos que llevan las comunicaciones, por idioma.

## Punto De Entrada

Menú **dre > Encargos > listados** (Pills2: ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Listados).

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_txt`
- `encargossacd.pantalla.listas_com_txt_get`
- `encargossacd.pantalla.listas_com_txt_update`

## Escenarios Inferidos

### Modificar los textos de las comunicaciones

Pasos propuestos:
1. Entrar en **listados** y elegir **modificar los textos de las comunicaciones**.
2. Elegir el tipo de texto y el idioma.
3. Editar el texto y pulsar **guardar**. Si se deja vacío, se elimina.

Endpoints asociados:
- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.clave`
- `form.comunicacion`
- `form.idioma`
- `html.comunicacion`
- `post.clave`
- `post.comunicacion`
- `post.idioma`

Acciones JavaScript:
- `fnjs_get_texto`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

