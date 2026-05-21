---
id: "shared.pantalla.tablaDB_lista_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "shared"
nombre: "TablaDB Lista Ver"
controller: "frontend/shared/controller/tablaDB_lista_ver.php"
vistas: ["frontend/shared/view/tablaDB_busqueda.phtml", "frontend/shared/view/tablaDB_lista_ver.phtml"]
fragmentos_frontend: ["frontend/shared/controller/tablaDB_lista_ver.php"]
endpoints: ["/src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos", "/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos"]
capacidades: ["shared.tablaDB_buscar.gestionar", "shared.tablaDB_lista.gestionar"]
campos: ["form.sel", "html.btn_new", "html.btn_ok", "html.k_buscar", "html.mod", "post.aSerieBuscar", "post.clase_info", "post.id_pau", "post.id_sel", "post.k_buscar", "post.mod", "post.obj_pau", "post.pau", "post.permiso", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario", "fnjs_nuevo"]
estado_revision: "generado"
---

# TablaDB Lista Ver

******************************************************************* ******** mostrar formulario de búsqueda ********************************************************************

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/shared/controller/tablaDB_lista_ver.php`

## Vistas Relacionadas

- `frontend/shared/view/tablaDB_busqueda.phtml`
- `frontend/shared/view/tablaDB_lista_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/shared/controller/tablaDB_lista_ver.php`

## Endpoints Usados

- `/src/shared/infrastructure/ui/http/controllers/tablaDB_buscar_datos`
- `/src/shared/tablaDB_buscar_datos`
- `/src/shared/tablaDB_lista_datos`

## Capacidades Relacionadas

- `shared.tablaDB_buscar.gestionar`
- `shared.tablaDB_lista.gestionar`

## Campos Detectados

- `form.sel`
- `html.btn_new`
- `html.btn_ok`
- `html.k_buscar`
- `html.mod`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.id_pau`
- `post.id_sel`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.pau`
- `post.permiso`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_nuevo`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
