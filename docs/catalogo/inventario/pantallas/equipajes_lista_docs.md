---
id: "inventario.pantalla.equipajes_lista_docs"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Lista docs EGM/lugar"
controller: "frontend/inventario/controller/equipajes_lista_docs.php"
vistas: ["frontend/inventario/view/equipajes_doc_maleta.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_egm", "/src/inventario/lista_docs_de_lugar"]
capacidades: ["inventario.lista_docs_de_egm.gestionar", "inventario.lista_docs_de_lugar.gestionar"]
campos: ["post.id_equipaje", "post.id_grupo", "post.id_item_egm", "post.id_lugar"]
acciones: ["fnjs_eliminar_grupo", "fnjs_modificar_form_add", "fnjs_modificar_form_del"]
estado_revision: "revisado"
---

# Lista docs EGM/lugar

Docs de maleta o lugar.


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

Ver [`manual/inventario.md`](../../../manual/inventario.md). Docs de maleta o lugar.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
