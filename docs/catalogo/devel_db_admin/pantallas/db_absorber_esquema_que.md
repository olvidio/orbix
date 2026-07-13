---
id: "devel_db_admin.pantalla.db_absorber_esquema_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Absorber Esquema Que"
controller: "frontend/devel_db_admin/controller/db_absorber_esquema_que.php"
vistas: ["frontend/devel_db_admin/view/db_absorber_esquema_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_absorber_esquema.php"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_propiedades.gestionar"]
campos: ["form.esquema_del", "form.esquema_matriz", "html.bimportar"]
acciones: ["fnjs_absorber_dl", "fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Db Absorber Esquema Que

Formulario para unir (absorber) un esquema DL en otro esquema matriz.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_absorber_esquema_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_absorber_esquema_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_absorber_esquema.php`

## Endpoints Usados

- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.esquema_del`
- `form.esquema_matriz`
- `html.bimportar`

## Acciones Detectadas

- `fnjs_absorber_dl`
- `fnjs_enviar_formulario`

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sistema > DB > DB unir esquemas
