---
id: "devel_db_admin.pantalla.db_mover_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Mover Que"
controller: "frontend/devel_db_admin/controller/db_mover_que.php"
vistas: ["frontend/devel_db_admin/view/db_mover_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_mover.php"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_propiedades.gestionar"]
campos: ["form.tabla", "html.bcrear"]
acciones: ["fnjs_db_mover_tabla", "fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Db Mover Que

Selección de tabla a mover de sv a sv-e por esquema.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_mover_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_mover_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_mover.php`

## Endpoints Usados

- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.tabla`
- `html.bcrear`

## Acciones Detectadas

- `fnjs_db_mover_tabla`
- `fnjs_enviar_formulario`

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sistema > DB > mover tabla a otra DB
- **Pills2:** sistema > DB > mover tabla a otra DB / ADMIN GLOBAL > DB > mover tabla a otra DB
