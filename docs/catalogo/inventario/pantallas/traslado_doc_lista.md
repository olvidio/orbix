---
id: "inventario.pantalla.traslado_doc_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Traslado Doc Lista"
controller: "frontend/inventario/controller/traslado_doc_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_ctr"]
capacidades: ["inventario.lista_docs_de_ctr.gestionar"]
campos: ["post.id_lugar", "post.id_ubi"]
acciones: ["fnjs_selectAll"]
estado_revision: "generado"
---

# Traslado Doc Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/traslado_doc_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_ctr`

## Capacidades Relacionadas

- `inventario.lista_docs_de_ctr.gestionar`

## Campos Detectados

- `post.id_lugar`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_selectAll`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
