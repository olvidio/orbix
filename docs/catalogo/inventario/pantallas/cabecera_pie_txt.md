---
id: "inventario.pantalla.cabecera_pie_txt"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "inventario"
nombre: "Textos cabecera/pie equipajes"
controller: "frontend/inventario/controller/cabecera_pie_txt.php"
vistas: ["frontend/inventario/view/cabecera_pie_txt.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/cabecera_pie_txt"]
capacidades: ["inventario.cabecera_pie_txt.gestionar"]
campos: ["form.cabecera", "form.cabeceraB", "form.firma", "form.pie", "html.cabecera", "html.cabeceraB", "html.firma", "html.pie"]
acciones: ["fnjs_guardar", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Textos cabecera/pie equipajes

Edita textos globales de impresión de equipajes.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/cabecera_pie_txt.php`

## Vistas Relacionadas

- `frontend/inventario/view/cabecera_pie_txt.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/cabecera_pie_txt`

## Capacidades Relacionadas

- `inventario.cabecera_pie_txt.gestionar`

## Campos Detectados

- `form.cabecera`
- `form.cabeceraB`
- `form.firma`
- `form.pie`
- `html.cabecera`
- `html.cabeceraB`
- `html.firma`
- `html.pie`

## Acciones Detectadas

- `fnjs_guardar`
- `fnjs_left_side_hide`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Edita textos globales de impresión de equipajes.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > tipos de texto
- **Pills2:** —
