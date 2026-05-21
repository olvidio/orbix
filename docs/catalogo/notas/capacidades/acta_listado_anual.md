---
id: "notas.acta_listado_anual.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta Listado Anual"
entidades: ["ListadoAnualActas"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_listado_anual_data"]
pantallas: ["frontend/notas/controller/acta_listado_anual.php"]
casos_uso: ["src\\notas\\application\\ListadoAnualActasData"]
tags: ["acta", "acta_listado_anual", "anual", "data", "listado", "notas"]
estado_revision: "generado"
---

# Gestionar Acta Listado Anual

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta_listado_anual`.

## Objetivo Funcional

Gestiona ListadoAnualActas. Lista las actas en un rango de fechas (ISO) ordenadas por nivel y fecha. En ambito rstgr considera todas las delegaciones de la region de stgr; en los demas ambitos, solo la delegacion actual. Cada item es un array asociativo {id_nivel, acta, f_acta, nombre_corto}.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/acta_listado_anual_data`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_listado_anual.php`

## Casos De Uso Detectados

- `src\notas\application\ListadoAnualActasData`

## Pistas Desde Endpoints

- Lista las actas en un rango de fechas (ISO) ordenadas por nivel y fecha. En ambito `rstgr` considera todas las delegaciones de la region de stgr; en los demas ambitos, solo la delegacion actual. Cada item es un array asociativo `{id_nivel, acta, f_acta, nombre_corto}`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
