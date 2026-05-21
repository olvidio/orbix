---
id: "inventario.pantalla.doc_de_dlb"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Doc De Dlb"
controller: "frontend/inventario/controller/doc_de_dlb.php"
vistas: ["frontend/inventario/view/doc_de_dlb.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/doc_asignar_dlb.php", "frontend/inventario/controller/doc_imprimir_dlb.php"]
endpoints: ["/src/inventario/lista_docs_de_dlb"]
capacidades: ["inventario.lista_docs_de_dlb.gestionar"]
campos: ["form.sel", "post.id_tipo_doc", "post.inventario"]
acciones: ["fnjs_go", "fnjs_selectAll"]
estado_revision: "generado"
---

# Doc De Dlb

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_de_dlb.php`

## Vistas Relacionadas

- `frontend/inventario/view/doc_de_dlb.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/doc_asignar_dlb.php`
- `frontend/inventario/controller/doc_imprimir_dlb.php`

## Endpoints Usados

- `/src/inventario/lista_docs_de_dlb`

## Capacidades Relacionadas

- `inventario.lista_docs_de_dlb.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- `fnjs_go`
- `fnjs_selectAll`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
