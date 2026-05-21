---
id: "devel_db_admin.pantalla.db_eliminar_esquema_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Eliminar Esquema Que"
controller: "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php"
vistas: ["frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_eliminar.php"]
endpoints: ["/src/devel_db_admin/db_lugar", "/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_lugar.gestionar", "devel_db_admin.db_propiedades.gestionar"]
campos: ["form.comun", "form.dl", "form.region", "form.sf", "form.sv", "html.beliminar", "html.comun", "html.sf", "html.sv"]
acciones: ["fnjs_db_eliminar", "fnjs_dl", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Db Eliminar Esquema Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_eliminar_esquema_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_eliminar_esquema_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_eliminar.php`

## Endpoints Usados

- `/src/devel_db_admin/db_lugar`
- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_lugar.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.comun`
- `form.dl`
- `form.region`
- `form.sf`
- `form.sv`
- `html.beliminar`
- `html.comun`
- `html.sf`
- `html.sv`

## Acciones Detectadas

- `fnjs_db_eliminar`
- `fnjs_dl`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
