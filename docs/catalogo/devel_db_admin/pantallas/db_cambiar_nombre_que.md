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
estado_revision: "generado"
---

# Db Cambiar Nombre Que

Descripcion funcional pendiente de revisar.

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
