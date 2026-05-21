---
id: "actividadessacd.sacds_encargados.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacds Encargados"
entidades: ["SacdsEncargados"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/sacds_encargados_data"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php"]
casos_uso: ["src\\actividadessacd\\application\\SacdsEncargadosData"]
tags: ["actividadessacd", "data", "encargados", "sacds", "sacds_encargados"]
estado_revision: "generado"
---

# Gestionar Sacds Encargados

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacds_encargados`.

## Objetivo Funcional

Gestiona SacdsEncargados. Devuelve los sacd encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadessacd/sacds_encargados_data`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdsEncargadosData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve los sacd encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
