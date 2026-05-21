---
id: "encargossacd.listas_com_txt.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas Com Txt"
entidades: ["ListasComTxt", "ListasComTxtGet"]
acciones: ["crear_actualizar", "obtener", "obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
pantallas: ["frontend/encargossacd/controller/listas_com_txt.php", "frontend/encargossacd/controller/listas_com_txt_get.php", "frontend/encargossacd/controller/listas_com_txt_update.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComTxtData", "src\\encargossacd\\application\\ListasComTxtGet", "src\\encargossacd\\application\\ListasComTxtUpdate"]
tags: ["com", "data", "encargossacd", "get", "listas", "listas_com_txt", "txt", "update"]
estado_revision: "generado"
---

# Gestionar Listas Com Txt

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_com_txt`.

## Objetivo Funcional

Gestiona ListasComTxt, ListasComTxtGet. Datos para la pantalla de textos de comunicacion (frontend/encargossacd/controller/listas_com_txt.php). Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (com_sacd / es). Lectura del texto de comunicacion para un par (clave, idioma). Extraido de EncargoTextoListasComAjax (rama que=get_texto) para eliminar el dispatcher multiproposito (criterio refactor.md). Mutacion del texto de comunicacion para un par (clave, idioma). Si el texto llega vacio, se elimina la fila. Extraido de EncargoTextoListasComAjax (rama que=update) para eliminar el dispatcher multiproposito (criterio refactor.md).

## Acciones Detectadas

- `crear_actualizar`
- `obtener`
- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_com_txt_data`
- `/src/encargossacd/listas_com_txt_get`
- `/src/encargossacd/listas_com_txt_update`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_com_txt.php`
- `frontend/encargossacd/controller/listas_com_txt_get.php`
- `frontend/encargossacd/controller/listas_com_txt_update.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasComTxtData`
- `src\encargossacd\application\ListasComTxtGet`
- `src\encargossacd\application\ListasComTxtUpdate`

## Pistas Desde Endpoints

- Datos para la pantalla de textos de comunicacion (`frontend/encargossacd/controller/listas_com_txt.php`). Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (`com_sacd` / `es`).
- Lectura del texto de comunicacion para un par (clave, idioma). Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).
- Mutacion del texto de comunicacion para un par (clave, idioma). Si el texto llega vacio, se elimina la fila. Extraido de `EncargoTextoListasComAjax` (rama `que=update`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
