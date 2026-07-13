---
id: "devel_db_admin.pantalla.db_cambiar_nombre_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Cambiar Nombre Que"
controller: "frontend/devel_db_admin/controller/db_cambiar_nombre_que.php"
vistas: ["frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php", "frontend/devel_db_admin/controller/db_renombrar_esquema.php", "frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php"]
endpoints: ["/src/devel_db_admin/db_lugar", "/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_lugar.gestionar", "devel_db_admin.db_propiedades.gestionar"]
campos: ["form.comun", "form.dl", "form.esquema_origen", "form.region", "form.sf", "form.sv", "html.bcorregir", "html.bcrear", "html.bverif", "html.comun", "html.dl", "html.esquema_origen", "html.region", "html.sf", "html.sv"]
acciones: ["fnjs_db_corregir_renombrar_esquema", "fnjs_db_renombrar_esquema", "fnjs_db_verificar_renombrar_esquema", "fnjs_dl", "fnjs_enviar_formulario", "fnjs_html_verificacion", "fnjs_sincronizar_frm_verif"]
estado_revision: "revisado"
---

# Db Cambiar Nombre Que

Asistente para renombrar un esquema DL: origen, región/dl destino y flags comun/sv/sf.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_cambiar_nombre_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_cambiar_nombre_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php`
- `frontend/devel_db_admin/controller/db_renombrar_esquema.php`
- `frontend/devel_db_admin/controller/db_verificar_renombrar_esquema.php`

## Endpoints Usados

- `/src/devel_db_admin/db_lugar`
- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_lugar.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.comun`
- `form.dl`
- `form.esquema_origen`
- `form.region`
- `form.sf`
- `form.sv`
- `html.bcorregir`
- `html.bcrear`
- `html.bverif`
- `html.comun`
- `html.dl`
- `html.esquema_origen`
- `html.region`
- `html.sf`
- `html.sv`

## Acciones Detectadas

- `fnjs_db_corregir_renombrar_esquema`
- `fnjs_db_renombrar_esquema`
- `fnjs_db_verificar_renombrar_esquema`
- `fnjs_dl`
- `fnjs_enviar_formulario`
- `fnjs_html_verificacion`
- `fnjs_sincronizar_frm_verif`

## Manual De Usuario

1. Acceder desde el menú de administración DB o configuración.
2. Completar el formulario y ejecutar la acción.
3. Revisar avisos/errores en el panel de respuesta.

## Ruta de menú

- **Legacy:** sistema > DB > cambiar nombre esquema
- **Pills2:** ADMIN GLOBAL > DB > mover y cambiar nombre dl / sistema > DB > cambiar nombre esquema
