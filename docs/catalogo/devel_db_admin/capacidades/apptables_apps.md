---
id: "devel_db_admin.apptables_apps.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Apptables Apps"
entidades: ["ApptablesApps"]
acciones: ["obtener_datos"]
endpoints: ["/src/devel_db_admin/apptables_apps_data"]
pantallas: ["frontend/devel_db_admin/controller/apptables.php"]
casos_uso: ["src\\devel_db_admin\\application\\ApptablesAppsData"]
tags: ["apps", "apptables", "apptables_apps", "data", "devel_db_admin"]
estado_revision: "generado"
---

# Gestionar Apptables Apps

Propuesta generada automaticamente a partir de endpoints con prefijo comun `apptables_apps`.

## Objetivo Funcional

Gestiona ApptablesApps. JSON con el mapa id_app → nombre para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/devel_db_admin/apptables_apps_data`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/apptables.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\ApptablesAppsData`

## Pistas Desde Endpoints

- JSON con el mapa `id_app` → nombre para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
