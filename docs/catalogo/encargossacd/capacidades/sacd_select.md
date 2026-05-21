---
id: "encargossacd.sacd_select.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Sacd Select"
entidades: ["SacdSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_select_data"]
pantallas: ["frontend/encargossacd/controller/sacd_ficha_ajax.php"]
casos_uso: ["src\\encargossacd\\application\\SacdSelectData"]
tags: ["data", "encargossacd", "sacd", "sacd_select", "select"]
estado_revision: "generado"
---

# Gestionar Sacd Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_select`.

## Objetivo Funcional

Gestiona SacdSelect. Opciones para el desplegable de SACDs filtrados por tabla (sacd_ficha_ajax?que=get_select).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/sacd_select_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Casos De Uso Detectados

- `src\encargossacd\application\SacdSelectData`

## Pistas Desde Endpoints

- Opciones para el desplegable de SACDs filtrados por tabla (`sacd_ficha_ajax?que=get_select`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
