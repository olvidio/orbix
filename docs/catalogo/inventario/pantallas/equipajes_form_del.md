---
id: "inventario.pantalla.equipajes_form_del"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Quitar doc de maleta"
controller: "frontend/inventario/controller/equipajes_form_del.php"
vistas: ["frontend/inventario/view/equipajes_form_del.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_egm"]
capacidades: ["inventario.lista_docs_de_egm.gestionar"]
campos: ["form.sel", "post.id_equipaje", "post.id_grupo", "post.id_item_egm"]
acciones: ["fnjs_cerrar", "fnjs_del_doc"]
estado_revision: "revisado"
---

# Quitar doc de maleta

Formulario del doc → `equipajes_del_doc`.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_form_del.php`

## Vistas Relacionadas

- `frontend/inventario/view/equipajes_form_del.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_docs_de_egm`

## Capacidades Relacionadas

- `inventario.lista_docs_de_egm.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_equipaje`
- `post.id_grupo`
- `post.id_item_egm`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_del_doc`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Formulario del doc → `equipajes_del_doc`.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
