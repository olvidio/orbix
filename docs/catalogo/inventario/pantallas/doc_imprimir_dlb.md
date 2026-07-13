---
id: "inventario.pantalla.doc_imprimir_dlb"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Imprimir inventario DLB"
controller: "frontend/inventario/controller/doc_imprimir_dlb.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/inventario_css_inline_data", "/src/inventario/inventario_dlb"]
capacidades: ["inventario.inventario_css_inline.gestionar", "inventario.inventario_dlb.gestionar"]
campos: ["post.dl", "post.sel"]
acciones: ["fnjs_ver_equipaje"]
estado_revision: "revisado"
---

# Imprimir inventario DLB

Vista de impresión DLB vía `inventario_dlb`.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/doc_imprimir_dlb.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/inventario_css_inline_data`
- `/src/inventario/inventario_dlb`

## Capacidades Relacionadas

- `inventario.inventario_css_inline.gestionar`
- `inventario.inventario_dlb.gestionar`

## Campos Detectados

- `post.dl`
- `post.sel`

## Acciones Detectadas

- `fnjs_ver_equipaje`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Vista de impresión DLB vía `inventario_dlb`.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
