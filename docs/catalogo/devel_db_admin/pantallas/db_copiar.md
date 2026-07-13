---
id: "devel_db_admin.pantalla.db_copiar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Copiar"
controller: "frontend/devel_db_admin/controller/db_copiar.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/devel_db_admin/copiar_esquema"]
capacidades: ["devel_db_admin.copiar_esquema.gestionar"]
campos: ["post.comun", "post.dl", "post.esquema", "post.region", "post.sf", "post.sv"]
acciones: []
estado_revision: "revisado"
---

# Db Copiar

Paso 3 del asistente: copia datos desde esquema de referencia (importar).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_copiar.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/devel_db_admin/copiar_esquema`

## Capacidades Relacionadas

- `devel_db_admin.copiar_esquema.gestionar`

## Campos Detectados

- `post.comun`
- `post.dl`
- `post.esquema`
- `post.region`
- `post.sf`
- `post.sv`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
