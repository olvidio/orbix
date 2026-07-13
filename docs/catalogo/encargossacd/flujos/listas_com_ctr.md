---
id: "encargossacd.listas_com_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas Com Ctr"
capacidad: "encargossacd.listas_com_ctr.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_com_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Com Ctr

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasComCtr. Datos para la comunicacion a los centros. Sustituye la logica de frontend/encargossacd/controller/listas_com_ctr.php. El modelo de salida replica el consumido por la vista listas_com_ctr.phtml: - array_atn_sacd[nombre_ubi] con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual. - origen_txt cabecera de emisor y lugar_fecha pie.

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

