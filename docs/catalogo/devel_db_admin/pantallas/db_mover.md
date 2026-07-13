---
id: "devel_db_admin.pantalla.db_mover"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Mover"
controller: "frontend/devel_db_admin/controller/db_mover.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/devel_db_admin/mover_tabla"]
capacidades: ["devel_db_admin.mover_tabla.gestionar"]
campos: ["post.tabla"]
acciones: []
estado_revision: "revisado"
---

# Db Mover

Fragmento que ejecuta el movimiento de tabla entre bases.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_mover.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/devel_db_admin/mover_tabla`

## Capacidades Relacionadas

- `devel_db_admin.mover_tabla.gestionar`

## Campos Detectados

- `post.tabla`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
