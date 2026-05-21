---
id: "ubis.delegaciones_region_stgr.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Delegaciones Region Stgr"
entidades: ["DelegacionesRegionStgr"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/delegaciones_region_stgr_data"]
pantallas: ["frontend/notas/controller/resumen_anual.php"]
casos_uso: ["src\\ubis\\application\\DelegacionesRegionStgrData"]
tags: ["data", "delegaciones", "delegaciones_region_stgr", "region", "stgr", "ubis"]
estado_revision: "generado"
---

# Gestionar Delegaciones Region Stgr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `delegaciones_region_stgr`.

## Objetivo Funcional

Gestiona DelegacionesRegionStgr. Delegaciones de una región STGR para desplegables (id_dl => sigla_dl).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/delegaciones_region_stgr_data`

## Pantallas Relacionadas

- `frontend/notas/controller/resumen_anual.php`

## Casos De Uso Detectados

- `src\ubis\application\DelegacionesRegionStgrData`

## Pistas Desde Endpoints

- Delegaciones de una región STGR para desplegables (id_dl => sigla_dl).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
