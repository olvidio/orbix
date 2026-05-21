---
id: "profesores.pantalla.ficha_profesor_stgr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "profesores"
nombre: "Ficha Profesor Stgr"
controller: "frontend/profesores/controller/ficha_profesor_stgr.php"
vistas: ["frontend/profesores/view/ficha_profesor_stgr.phtml", "frontend/profesores/view/ficha_profesor_stgr.print.phtml"]
fragmentos_frontend: []
endpoints: ["/src/profesores/ficha_profesor_stgr"]
capacidades: ["profesores.ficha_profesor_stgr.gestionar"]
campos: ["post.depende", "post.id_nom", "post.id_pau", "post.id_tabla", "post.obj_pau", "post.permiso", "post.print", "post.sel", "post.stack"]
acciones: ["fnjs_update_div"]
estado_revision: "generado"
---

# Ficha Profesor Stgr

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/profesores/controller/ficha_profesor_stgr.php`

## Vistas Relacionadas

- `frontend/profesores/view/ficha_profesor_stgr.phtml`
- `frontend/profesores/view/ficha_profesor_stgr.print.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/profesores/ficha_profesor_stgr`

## Capacidades Relacionadas

- `profesores.ficha_profesor_stgr.gestionar`

## Campos Detectados

- `post.depende`
- `post.id_nom`
- `post.id_pau`
- `post.id_tabla`
- `post.obj_pau`
- `post.permiso`
- `post.print`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
