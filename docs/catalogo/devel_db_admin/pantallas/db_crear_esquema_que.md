---
id: "devel_db_admin.pantalla.db_crear_esquema_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Crear Esquema Que"
controller: "frontend/devel_db_admin/controller/db_crear_esquema_que.php"
vistas: ["frontend/devel_db_admin/view/db_crear_esquema_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_copiar.php", "frontend/devel_db_admin/controller/db_crear_esquema.php", "frontend/devel_db_admin/controller/db_crear_usuarios.php"]
endpoints: ["/src/devel_db_admin/db_lugar", "/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_lugar.gestionar", "devel_db_admin.db_propiedades.gestionar"]
campos: ["form.comun", "form.dl", "form.esquema", "form.region", "form.sf", "form.sv", "html.bcrear", "html.bcrear_esquema", "html.bimportar", "html.comun", "html.sf", "html.sv"]
acciones: ["fnjs_db_copiar", "fnjs_db_crear_esquemas", "fnjs_db_crear_usuarios", "fnjs_dl", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Db Crear Esquema Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_crear_esquema_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_crear_esquema_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_copiar.php`
- `frontend/devel_db_admin/controller/db_crear_esquema.php`
- `frontend/devel_db_admin/controller/db_crear_usuarios.php`

## Endpoints Usados

- `/src/devel_db_admin/db_lugar`
- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_lugar.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.comun`
- `form.dl`
- `form.esquema`
- `form.region`
- `form.sf`
- `form.sv`
- `html.bcrear`
- `html.bcrear_esquema`
- `html.bimportar`
- `html.comun`
- `html.sf`
- `html.sv`

## Acciones Detectadas

- `fnjs_db_copiar`
- `fnjs_db_crear_esquemas`
- `fnjs_db_crear_usuarios`
- `fnjs_dl`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
