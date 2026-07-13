---
id: "inventario.pantalla.equipajes_form_add"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Añadir doc a maleta"
controller: "frontend/inventario/controller/equipajes_form_add.php"
vistas: ["frontend/inventario/view/equipajes_form_add.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_tipo_doc"]
capacidades: ["inventario.lista_tipo_doc.gestionar"]
campos: ["form.id_tipo_doc", "form.sel", "post.id_equipaje", "post.id_grupo", "post.id_item_egm"]
acciones: ["fnjs_add_doc", "fnjs_cerrar", "fnjs_docs_libres"]
estado_revision: "revisado"
---

# Añadir doc a maleta

Formulario add doc → `equipajes_add_doc`.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_add.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_add.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_tipo_doc`

## Capacidades Relacionadas

- `inventario.lista_tipo_doc.gestionar`

## Campos Detectados

- `form.id_tipo_doc`
- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_add_doc`
- `fnjs_cerrar`
- `fnjs_docs_libres`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Formulario add doc → `equipajes_add_doc`.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
