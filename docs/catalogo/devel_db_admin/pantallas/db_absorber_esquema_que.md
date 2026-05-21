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
estado_revision: "generado"
---

# Db Absorber Esquema Que

Descripcion funcional pendiente de revisar.

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
