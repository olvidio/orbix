---
id: "notas.pantalla.tessera_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Tessera Imprimir"
controller: "frontend/notas/controller/tessera_imprimir.php"
vistas: []
fragmentos_frontend: ["frontend/notas/controller/tessera_2_mpdf.php", "frontend/notas/controller/tessera_imprimir.php"]
endpoints: ["/src/notas/tessera_imprimir_data"]
capacidades: ["notas.tessera_imprimir.gestionar"]
campos: ["post.cara", "post.id_nom", "post.id_tabla", "post.refresh", "post.sel", "post.stack"]
acciones: ["fnjs_left_side_hide", "fnjs_update_div"]
estado_revision: "revisado"
---

# Tessera Imprimir

Impresión HTML de tessera.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/tessera_imprimir.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/tessera_2_mpdf.php`
- `frontend/notas/controller/tessera_imprimir.php`

## Endpoints Usados

- `/src/notas/tessera_imprimir_data`

## Capacidades Relacionadas

- `notas.tessera_imprimir.gestionar`

## Campos Detectados

- `post.cara`
- `post.id_nom`
- `post.id_tabla`
- `post.refresh`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
