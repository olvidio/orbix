---
id: "inventario.pantalla.equipajes_ver_docs"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Ver Docs"
controller: "frontend/inventario/controller/equipajes_ver_docs.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_lugar"]
capacidades: ["inventario.lista_docs_de_lugar.gestionar"]
campos: ["form.sel", "post.id_equipaje", "post.id_grupo", "post.nom_grupo"]
acciones: ["fnjs_update_grupo"]
estado_revision: "generado"
---

# Equipajes Ver Docs

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_ver_docs.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_lugar`

## Capacidades Relacionadas

- `inventario.lista_docs_de_lugar.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.nom_grupo`

## Acciones Detectadas

- `fnjs_update_grupo`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
