---
id: "actividadessacd.comunicacion_activ_sacd.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Comunicacion Activ Sacd"
entidades: ["ComunicacionActividadesSacd"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_data"]
pantallas: ["frontend/actividadessacd/controller/com_sacd_activ_periodo.php", "frontend/actividadessacd/view/com_sacd_activ_periodo.phtml"]
casos_uso: ["src\\actividadessacd\\application\\ComunicacionActividadesSacdData"]
tags: ["activ", "actividadessacd", "comunicacion", "comunicacion_activ_sacd", "data", "sacd"]
estado_revision: "generado"
---

# Gestionar Comunicacion Activ Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `comunicacion_activ_sacd`.

## Objetivo Funcional

Gestiona ComunicacionActividadesSacd. Construye el listado de atencion de actividades a comunicar a los sacd (incluidas las de los "sacd de paso" cuando procede).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadessacd/comunicacion_activ_sacd_data`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/com_sacd_activ_periodo.php`
- `frontend/actividadessacd/view/com_sacd_activ_periodo.phtml`

## Casos De Uso Detectados

- `src\actividadessacd\application\ComunicacionActividadesSacdData`

## Pistas Desde Endpoints

- Endpoint backend: construye el listado de atencion de actividades a comunicar a los sacd (incluidas las de los "sacd de paso" cuando procede).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
