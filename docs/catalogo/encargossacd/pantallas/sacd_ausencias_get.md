---
id: "encargossacd.pantalla.sacd_ausencias_get"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Sacd Ausencias Get"
controller: "frontend/encargossacd/controller/sacd_ausencias_get.php"
vistas: ["frontend/encargossacd/view/sacd_ausencias_get.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/sacd_ausencias_get.php", "frontend/encargossacd/controller/sacd_ausencias_update.php"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
capacidades: ["encargossacd.sacd_ausencias_get.gestionar"]
campos: ["form.fin", "form.id_enc", "form.id_item", "form.inicio", "html.ok", "post.filtro_sacd", "post.historial", "post.id_nom"]
acciones: ["fnjs_date_fin", "fnjs_guardar", "fnjs_horario", "fnjs_mas_enc", "fnjs_update_div"]
estado_revision: "revisado"
---

# Sacd Ausencias Get

Muestra las ausencias de un SACD.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/sacd_ausencias_get.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/sacd_ausencias_get.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/sacd_ausencias_get.php`
- `frontend/encargossacd/controller/sacd_ausencias_update.php`

## Endpoints Usados

- `/src/encargossacd/sacd_ausencias_get_data`

## Capacidades Relacionadas

- `encargossacd.sacd_ausencias_get.gestionar`

## Campos Detectados

- `form.fin`
- `form.id_enc`
- `form.id_item`
- `form.inicio`
- `html.ok`
- `post.filtro_sacd`
- `post.historial`
- `post.id_nom`

## Acciones Detectadas

- `fnjs_date_fin`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_mas_enc`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

