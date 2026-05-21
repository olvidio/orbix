---
id: "encargossacd.listas_exigencia_ctr.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Listas Exigencia Ctr"
entidades: ["ListasExigenciaCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/listas_exigencia_ctr_data"]
pantallas: ["frontend/encargossacd/controller/listas_exigencia_ctr.php"]
casos_uso: ["src\\encargossacd\\application\\ListasExigenciaCtrData"]
tags: ["ctr", "data", "encargossacd", "exigencia", "listas", "listas_exigencia_ctr"]
estado_revision: "generado"
---

# Gestionar Listas Exigencia Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `listas_exigencia_ctr`.

## Objetivo Funcional

Gestiona ListasExigenciaCtr. Listado de exigencias SACD por centro/iglesia. Sustituye la logica de frontend/encargossacd/controller/listas_exigencia_ctr.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/listas_exigencia_ctr_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/listas_exigencia_ctr.php`

## Casos De Uso Detectados

- `src\encargossacd\application\ListasExigenciaCtrData`

## Pistas Desde Endpoints

- Listado de exigencias SACD por centro/iglesia. Sustituye la logica de `frontend/encargossacd/controller/listas_exigencia_ctr.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
