---
id: "devel_db_admin.db_lugar.gestionar"
tipo: "capacidad"
modulo: "devel_db_admin"
nombre: "Gestionar Db Lugar"
entidades: ["DbLugarDropdown"]
acciones: ["ejecutar"]
endpoints: ["/src/devel_db_admin/db_lugar"]
pantallas: ["frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php", "frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml", "frontend/devel_db_admin/view/db_crear_esquema_que.phtml", "frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml"]
casos_uso: ["src\\devel_db_admin\\application\\DbLugarDropdown"]
tags: ["db", "db_lugar", "devel_db_admin", "lugar"]
estado_revision: "generado"
---

# Gestionar Db Lugar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `db_lugar`.

## Objetivo Funcional

Gestiona DbLugarDropdown. Fragmento HTML: desplegable dl según region (POST), para AJAX en db_que / db_cambiar_nombre_que.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/devel_db_admin/db_lugar`

## Pantallas Relacionadas

- `frontend/devel_db_admin/controller/db_cambiar_nombre_que.php`
- `frontend/devel_db_admin/controller/db_crear_esquema_que.php`
- `frontend/devel_db_admin/controller/db_eliminar_esquema_que.php`
- `frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml`
- `frontend/devel_db_admin/view/db_crear_esquema_que.phtml`
- `frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml`

## Casos De Uso Detectados

- `src\devel_db_admin\application\DbLugarDropdown`

## Pistas Desde Endpoints

- Fragmento HTML: desplegable `dl` según `region` (POST), para AJAX en db_que / db_cambiar_nombre_que.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
