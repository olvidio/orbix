---
id: "devel_db_admin.pantalla.db_absorber_esquema"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Absorber Esquema"
controller: "frontend/devel_db_admin/controller/db_absorber_esquema.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/devel_db_admin/absorber_esquema"]
capacidades: ["devel_db_admin.absorber_esquema.gestionar"]
campos: ["post.esquema_del", "post.esquema_matriz"]
acciones: []
estado_revision: "revisado"
---

# Db Absorber Esquema

Fragmento AJAX que ejecuta la absorción de esquema tras confirmar en `db_absorber_esquema_que`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_absorber_esquema.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/devel_db_admin/absorber_esquema`

## Capacidades Relacionadas

- `devel_db_admin.absorber_esquema.gestionar`

## Campos Detectados

- `post.esquema_del`
- `post.esquema_matriz`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
