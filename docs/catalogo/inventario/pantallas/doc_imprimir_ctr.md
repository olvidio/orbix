---
id: "inventario.pantalla.doc_imprimir_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Doc Imprimir Ctr"
controller: "frontend/inventario/controller/doc_imprimir_ctr.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/inventario_css_inline_data", "/src/inventario/inventario_ctr"]
capacidades: ["inventario.inventario_css_inline.gestionar", "inventario.inventario_ctr.gestionar"]
campos: ["post.dl", "post.sel"]
acciones: ["fnjs_ver_equipaje"]
estado_revision: "generado"
---

# Doc Imprimir Ctr

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_imprimir_ctr.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/inventario_css_inline_data`
- `/src/inventario/inventario_ctr`

## Capacidades Relacionadas

- `inventario.inventario_css_inline.gestionar`
- `inventario.inventario_ctr.gestionar`

## Campos Detectados

- `post.dl`
- `post.sel`

## Acciones Detectadas

- `fnjs_ver_equipaje`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
