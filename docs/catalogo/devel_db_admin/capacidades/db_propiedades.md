---
id: "devel_db_admin.db_propiedades.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Db Propiedades"
entidades: ["DbPropiedades"]
acciones: ["obtener_datos"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
pantallas: ["frontend/devel_db_admin/controller/apptables.php", "frontend/devel_db_admin/controller/db_absorber_esquema_que.php", "frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php", "frontend/devel_db_admin/controller/db_mover_que.php"]
casos_uso: ["src\\devel_db_admin\\application\\DbPropiedadesFormData"]
tags: ["data", "db", "db_propiedades", "devel_db_admin", "propiedades"]
estado_revision: "generado"
---

# Gestionar Db Propiedades

Propuesta generada automaticamente a partir de endpoints con prefijo comun `db_propiedades`.

## Objetivo Funcional

Gestiona DbPropiedades. JSON para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/devel_db_admin/db_propiedades_data`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/apptables.php`
- `frontend/devel_db_admin/controller/db_absorber_esquema_que.php`
- `frontend/devel_db_admin/controller/db_cambiar_nombre_que.php`
- `frontend/devel_db_admin/controller/db_crear_esquema_que.php`
- `frontend/devel_db_admin/controller/db_eliminar_esquema_que.php`
- `frontend/devel_db_admin/controller/db_mover_que.php`

## Casos De Uso Detectados

- `src\devel_db_admin\application\DbPropiedadesFormData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
