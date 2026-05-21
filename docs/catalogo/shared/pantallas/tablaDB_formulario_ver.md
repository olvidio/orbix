---
id: "shared.pantalla.tablaDB_formulario_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "shared"
nombre: "TablaDB Formulario Ver"
controller: "frontend/shared/controller/tablaDB_formulario_ver.php"
vistas: ["frontend/shared/view/tablaDB_formulario.phtml"]
fragmentos_frontend: ["frontend/dossiers/controller/dossiers_ver.php"]
endpoints: ["/src/shared/tablaDB_depende_datos", "/src/shared/tablaDB_formulario_datos", "/src/shared/tablaDB_lista_datos", "/src/shared/tablaDB_update"]
capacidades: ["shared.tablaDB.gestionar", "shared.tablaDB_depende.gestionar", "shared.tablaDB_formulario.gestionar", "shared.tablaDB_lista.gestionar"]
campos: ["form.accion", "form.clase_info", "form.valor_depende", "html.<?= $nom_camp ?>", "post.aSerieBuscar", "post.clase_info", "post.datos_buscar", "post.id_pau", "post.k_buscar", "post.mod", "post.obj_pau", "post.permiso", "post.sel"]
acciones: ["fnjs_actualizar_depende", "fnjs_cancelar", "fnjs_comprobar_fecha", "fnjs_grabar"]
estado_revision: "generado"
---

# TablaDB Formulario Ver

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/shared/controller/tablaDB_formulario_ver.php`

## Vistas Relacionadas

- `frontend/shared/view/tablaDB_formulario.phtml`

## Fragmentos Frontend Relacionados

- `frontend/dossiers/controller/dossiers_ver.php`

## Endpoints Usados

- `/src/shared/tablaDB_depende_datos`
- `/src/shared/tablaDB_formulario_datos`
- `/src/shared/tablaDB_lista_datos`
- `/src/shared/tablaDB_update`

## Capacidades Relacionadas

- `shared.tablaDB.gestionar`
- `shared.tablaDB_depende.gestionar`
- `shared.tablaDB_formulario.gestionar`
- `shared.tablaDB_lista.gestionar`

## Campos Detectados

- `form.accion`
- `form.clase_info`
- `form.valor_depende`
- `html.<?= $nom_camp ?>`
- `post.aSerieBuscar`
- `post.clase_info`
- `post.datos_buscar`
- `post.id_pau`
- `post.k_buscar`
- `post.mod`
- `post.obj_pau`
- `post.permiso`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar_depende`
- `fnjs_cancelar`
- `fnjs_comprobar_fecha`
- `fnjs_grabar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
