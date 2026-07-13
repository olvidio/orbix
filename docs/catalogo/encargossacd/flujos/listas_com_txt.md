---
id: "encargossacd.listas_com_txt.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Listas Com Txt"
capacidad: "encargossacd.listas_com_txt.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_com_txt", "encargossacd.pantalla.listas_com_txt_get", "encargossacd.pantalla.listas_com_txt_update"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
estado_revision: "revisado"
---

# Flujo - Gestionar Listas Com Txt

Propuesta generada automaticamente desde la capacidad `encargossacd.listas_com_txt.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListasComTxt, ListasComTxtGet. Datos para la pantalla de textos de comunicacion (frontend/encargossacd/controller/listas_com_txt.php). Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (com_sacd / es). Lectura del texto de comunicacion para un par (clave, idioma). Extraido de EncargoTextoListasComAjax (rama que=get_texto) para eliminar el dispatcher multiproposito (criterio refactor.md). Mutacion del texto de comunicacion para un par (clave, idioma). Si el texto llega vacio, se elimina la fila. Extraido de EncargoTextoListasComAjax (rama que=update) para eliminar el dispatcher multiproposito (criterio refactor.md).

## Punto De Entrada

Fragmento AJAX embebido; sin entrada de menú directa.


## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.listas_com_txt`
- `encargossacd.pantalla.listas_com_txt_get`
- `encargossacd.pantalla.listas_com_txt_update`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/encargossacd/listas_com_txt_update`

### Obtener

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

