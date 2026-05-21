---
id: "actividadplazas.plazas_ceder.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Plazas Ceder"
entidades: ["PlazasCeder"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/plazas_ceder"]
pantallas: ["frontend/actividadplazas/controller/resumen_plazas.php"]
casos_uso: ["src\\actividadplazas\\application\\PlazasCeder"]
tags: ["actividadplazas", "ceder", "plazas", "plazas_ceder"]
estado_revision: "generado"
---

# Gestionar Plazas Ceder

Propuesta generada automaticamente a partir de endpoints con prefijo comun `plazas_ceder`.

## Objetivo Funcional

Gestiona PlazasCeder. Actualiza el array cedidas de ActividadPlazasDl para ceder (o quitar) plazas de mi_dele a otra dl en una actividad.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadplazas/plazas_ceder`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/resumen_plazas.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\PlazasCeder`

## Pistas Desde Endpoints

- Endpoint backend: actualiza el array `cedidas` de `ActividadPlazasDl` para ceder (o quitar) plazas de `mi_dele` a otra dl en una actividad.

## Errores Conocidos

- `faltan parametros id_activ / region_dl`
- `hay un error, no se ha guardado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
