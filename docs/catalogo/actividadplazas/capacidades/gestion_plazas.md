---
id: "actividadplazas.gestion_plazas.gestionar"
tipo: "capacidad"
modulo: "actividadplazas"
nombre: "Gestionar Gestion Plazas"
entidades: ["GestionPlazas"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
pantallas: ["frontend/actividadplazas/controller/gestion_plazas.php", "frontend/actividadplazas/controller/plazas_balance_dl.php"]
casos_uso: ["src\\actividadplazas\\application\\GestionPlazasData", "src\\actividadplazas\\application\\GestionPlazasUpdate"]
tags: ["actividadplazas", "data", "gestion", "gestion_plazas", "plazas", "update"]
estado_revision: "generado"
---

# Gestionar Gestion Plazas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `gestion_plazas`.

## Objetivo Funcional

Gestiona GestionPlazas. Actualiza las plazas (totales, concedidas o pedidas) desde la edicion inline de frontend\shared\web\TablaEditable. Devuelve los datos del cuadro de gestion de plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo) para que el controller frontend monte el frontend\shared\web\TablaEditable.

## Acciones Detectadas

- `crear_actualizar`
- `obtener_datos`

## Endpoints

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Pantallas Relacionadas

- `frontend/actividadplazas/controller/gestion_plazas.php`
- `frontend/actividadplazas/controller/plazas_balance_dl.php`

## Casos De Uso Detectados

- `src\actividadplazas\application\GestionPlazasData`
- `src\actividadplazas\application\GestionPlazasUpdate`

## Pistas Desde Endpoints

- Endpoint backend: actualiza las plazas (totales, concedidas o pedidas) desde la edicion inline de `frontend\shared\web\TablaEditable`.
- Endpoint backend: devuelve los datos del cuadro de gestion de plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo) para que el controller frontend monte el `frontend\shared\web\TablaEditable`.

## Errores Conocidos

- `no se encuentra la actividad`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
