---
id: "inventario.pantalla.cabecera_pie_txt"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Cabecera Pie Txt"
controller: "frontend/inventario/controller/cabecera_pie_txt.php"
vistas: ["frontend/inventario/view/cabecera_pie_txt.phtml"]
fragmentos_frontend: []
endpoints: ["/src/inventario/cabecera_pie_txt"]
capacidades: ["inventario.cabecera_pie_txt.gestionar"]
campos: ["form.cabecera", "form.cabeceraB", "form.firma", "form.pie", "html.cabecera", "html.cabeceraB", "html.firma", "html.pie"]
acciones: ["fnjs_guardar", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Cabecera Pie Txt

Descripcion funcional pendiente de revisar.

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
