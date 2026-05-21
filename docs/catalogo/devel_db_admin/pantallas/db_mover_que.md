---
id: "devel_db_admin.pantalla.db_mover_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "devel_db_admin"
nombre: "Db Mover Que"
controller: "frontend/devel_db_admin/controller/db_mover_que.php"
vistas: ["frontend/devel_db_admin/view/db_mover_que.phtml"]
fragmentos_frontend: ["frontend/devel_db_admin/controller/db_mover.php"]
endpoints: ["/src/devel_db_admin/db_propiedades_data"]
capacidades: ["devel_db_admin.db_propiedades.gestionar"]
campos: ["form.tabla", "html.bcrear"]
acciones: ["fnjs_db_mover_tabla", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Db Mover Que

Para mover una tabla de la DB sv a sv-e que está en la dmz.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/devel_db_admin/controller/db_mover_que.php`

## Vistas Relacionadas

- `frontend/devel_db_admin/view/db_mover_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/devel_db_admin/controller/db_mover.php`

## Endpoints Usados

- `/src/devel_db_admin/db_propiedades_data`

## Capacidades Relacionadas

- `devel_db_admin.db_propiedades.gestionar`

## Campos Detectados

- `form.tabla`
- `html.bcrear`

## Acciones Detectadas

- `fnjs_db_mover_tabla`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
