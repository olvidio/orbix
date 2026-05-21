---
id: "actividadplazas.resumen_plazas.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Resumen Plazas"
entidades: ["ResumenPlazas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
pantallas: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\ResumenPlazasData"]
tags: ["actividadplazas", "data", "plazas", "resumen", "resumen_plazas"]
estado_revision: "generado"
---

# Gestionar Resumen Plazas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `resumen_plazas`.

## Objetivo Funcional

Gestiona ResumenPlazas. Datos del resumen de plazas por actividad (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) + opciones del desplegable para "ceder" y flags publicado/otra_dl.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadplazas/resumen_plazas_data`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\ResumenPlazasData`

## Pistas Desde Endpoints

- Endpoint backend: datos del resumen de plazas por actividad (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) + opciones del desplegable para "ceder" y flags publicado/otra_dl.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
