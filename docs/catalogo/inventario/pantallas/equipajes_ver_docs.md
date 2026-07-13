---
id: "inventario.pantalla.equipajes_ver_docs"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Ver docs de lugar"
controller: "frontend/inventario/controller/equipajes_ver_docs.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_docs_de_lugar"]
capacidades: ["inventario.lista_docs_de_lugar.gestionar"]
campos: ["form.sel", "post.id_equipaje", "post.id_grupo", "post.nom_grupo"]
acciones: ["fnjs_update_grupo"]
estado_revision: "revisado"
---

# Ver docs de lugar

Lista documentos de un lugar en equipaje.


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

Ver [`manual/inventario.md`](../../../manual/inventario.md). Lista documentos de un lugar en equipaje.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
