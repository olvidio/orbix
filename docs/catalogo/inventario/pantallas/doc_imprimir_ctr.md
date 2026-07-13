---
id: "inventario.pantalla.doc_imprimir_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Imprimir inventario centros"
controller: "frontend/inventario/controller/doc_imprimir_ctr.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/inventario_css_inline_data", "/src/inventario/inventario_ctr"]
capacidades: ["inventario.inventario_css_inline.gestionar", "inventario.inventario_ctr.gestionar"]
campos: ["post.dl", "post.sel"]
acciones: ["fnjs_ver_equipaje"]
estado_revision: "revisado"
---

# Imprimir inventario centros

Vista de impresión: llama `inventario_ctr` + CSS inline.


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

Ver [`manual/inventario.md`](../../../manual/inventario.md). Vista de impresión: llama `inventario_ctr` + CSS inline.

## Ruta de menú

- **Legacy:** sin entrada de menú (destino impresión)
- **Pills2:** —
