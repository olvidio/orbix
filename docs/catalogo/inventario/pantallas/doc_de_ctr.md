---
id: "inventario.pantalla.doc_de_ctr"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Inventario por centros"
controller: "frontend/inventario/controller/doc_de_ctr.php"
vistas: ["frontend/inventario/view/doc_de_ctr.phtml"]
fragmentos_frontend: ["frontend/inventario/controller/doc_asignar_ctr.php", "frontend/inventario/controller/doc_imprimir_ctr.php"]
endpoints: ["/src/inventario/lista_de_ctr_con_docs"]
capacidades: ["inventario.lista_de_ctr_con_docs.gestionar"]
campos: ["form.sel", "post.id_tipo_doc", "post.inventario"]
acciones: ["fnjs_go", "fnjs_selectAll"]
estado_revision: "revisado"
---

# Inventario por centros

Selección de tipo doc y centros; enlaces a asignar, modificar e imprimir inventario CTR.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_de_ctr.php`

## Vistas Relacionadas

- `frontend/inventario/view/doc_de_ctr.phtml`

## Fragmentos Frontend Relacionados

- `frontend/inventario/controller/doc_asignar_ctr.php`
- `frontend/inventario/controller/doc_imprimir_ctr.php`

## Endpoints Usados

- `/src/inventario/lista_de_ctr_con_docs`

## Capacidades Relacionadas

- `inventario.lista_de_ctr_con_docs.gestionar`

## Campos Detectados

- `form.sel`
- `post.id_tipo_doc`
- `post.inventario`

## Acciones Detectadas

- `fnjs_go`
- `fnjs_selectAll`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Selección de tipo doc y centros; enlaces a asignar, modificar e imprimir inventario CTR.

## Ruta de menú

- **Legacy:** sin entrada directa (desde inventario_que)
- **Pills2:** —
