---
id: "devel_db_admin.pantalla.db_crear_usuarios"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Crear Usuarios"
controller: "frontend/devel_db_admin/controller/db_crear_usuarios.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/devel_db_admin/crear_usuarios"]
capacidades: ["devel_db_admin.crear_usuarios.gestionar"]
campos: ["post.dl", "post.region"]
acciones: []
estado_revision: "revisado"
---

# Db Crear Usuarios

Paso 1 del asistente: crea roles PostgreSQL y passwords en ficheros .inc.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_crear_usuarios.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/devel_db_admin/crear_usuarios`

## Capacidades Relacionadas

- `devel_db_admin.crear_usuarios.gestionar`

## Campos Detectados

- `post.dl`
- `post.region`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
