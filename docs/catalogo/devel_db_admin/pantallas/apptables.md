---
id: "devel_db_admin.pantalla.apptables"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Apptables"
controller: "frontend/devel_db_admin/controller/apptables.php"
vistas: ["frontend/devel_db_admin/view/apptables.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/apptables_update.php"]
endpoints: ["/src/devel_db_admin/apptables_apps_data", "/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.apptables_apps.gestionar", "devel_db_admin.db_propiedades.gestionar"]
campos: ["form.esquema", "form.id_app", "html.bce", "html.bcg", "html.bee", "html.beg"]
acciones: ["fnjs_db", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Apptables

La idea de esta página es poder crear y eliminar las tablas correspondientes a cada app.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/apptables.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/apptables.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/apptables_update.php`

## Endpoints Usados

- `/src/devel_db_admin/apptables_apps_data`
- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.apptables_apps.gestionar`
- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.esquema`
- `form.id_app`
- `html.bce`
- `html.bcg`
- `html.bee`
- `html.beg`

## Acciones Detectadas

- `fnjs_db`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
