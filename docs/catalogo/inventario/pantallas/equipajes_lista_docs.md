---
id: "inventario.pantalla.equipajes_lista_docs"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Lista Docs"
controller: "frontend/inventario/controller/equipajes_lista_docs.php"
vistas: ["frontend/inventario/view/equipajes_doc_maleta.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_egm", "/src/inventario/lista_docs_de_lugar"]
capacidades: ["inventario.lista_docs_de_egm.gestionar", "inventario.lista_docs_de_lugar.gestionar"]
campos: ["post.id_equipaje", "post.id_grupo", "post.id_item_egm", "post.id_lugar"]
acciones: ["fnjs_eliminar_grupo", "fnjs_modificar_form_add", "fnjs_modificar_form_del"]
estado_revision: "generado"
---

# Equipajes Lista Docs

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_lista_docs.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_doc_maleta.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_egm`
- `/src/inventario/lista_docs_de_lugar`

## Capacidades Relacionadas

- `inventario.lista_docs_de_egm.gestionar`
- `inventario.lista_docs_de_lugar.gestionar`

## Campos Detectados

- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`
- `post.id_lugar`

## Acciones Detectadas

- `fnjs_eliminar_grupo`
- `fnjs_modificar_form_add`
- `fnjs_modificar_form_del`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
