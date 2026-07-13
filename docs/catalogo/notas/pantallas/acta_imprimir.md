---
id: "notas.pantalla.acta_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Acta Imprimir"
controller: "frontend/notas/controller/acta_imprimir.php"
vistas: ["frontend/notas/view/acta_imprimir.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_2_mpdf.php", "frontend/notas/controller/acta_imprimir.php"]
endpoints: ["/src/notas/acta_imprimir_presentacion_data"]
capacidades: ["notas.acta_imprimir_presentacion.gestionar"]
campos: ["form.acta", "post.acta", "post.cara", "post.refresh", "post.sel"]
acciones: ["fnjs_left_side_hide", "fnjs_update_div"]
estado_revision: "revisado"
---

# Acta Imprimir

Presentación HTML para impresión de acta seleccionada.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/acta_imprimir.php`

## Vistas Relacionadas

- `frontend/notas/view/acta_imprimir.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_2_mpdf.php`
- `frontend/notas/controller/acta_imprimir.php`

## Endpoints Usados

- `/src/notas/acta_imprimir_presentacion_data`

## Capacidades Relacionadas

- `notas.acta_imprimir_presentacion.gestionar`

## Campos Detectados

- `form.acta`
- `post.acta`
- `post.cara`
- `post.refresh`
- `post.sel`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
