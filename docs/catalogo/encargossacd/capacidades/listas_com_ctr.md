---
id: "encargossacd.listas_com_ctr.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas Com Ctr"
entidades: ["ListasComCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
pantallas: ["frontend/encargossacd/controller/listas_com_ctr.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComCtrData"]
tags: ["com", "ctr", "data", "encargossacd", "listas", "listas_com_ctr"]
estado_revision: "generado"
---

# Gestionar Listas Com Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_com_ctr`.

## Objetivo Funcional

Gestiona ListasComCtr. Datos para la comunicacion a los centros. Sustituye la logica de frontend/encargossacd/controller/listas_com_ctr.php. El modelo de salida replica el consumido por la vista listas_com_ctr.phtml: - array_atn_sacd[nombre_ubi] con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual. - origen_txt cabecera de emisor y lugar_fecha pie.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_com_ctr_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_com_ctr.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasComCtrData`

## Pistas Desde Endpoints

- Datos para la comunicacion a los centros. Sustituye la logica de `frontend/encargossacd/controller/listas_com_ctr.php`. El modelo de salida replica el consumido por la vista `listas_com_ctr.phtml`: - `array_atn_sacd[nombre_ubi]` con titular, suplente, colaboradores y el texto de comunicacion traducido al idioma del idioma actual. - `origen_txt` cabecera de emisor y `lugar_fecha` pie.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
