---
id: "notas.pantalla.resumen_anual"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Resumen Anual"
controller: "frontend/notas/controller/resumen_anual.php"
vistas: ["frontend/notas/view/resumen_anual.phtml"]
fragmentos_frontend: ["frontend/notas/controller/asig_faltan_que.php", "frontend/notas/controller/comprobar_notas.php", "frontend/notas/controller/informe_stgr_agd.php", "frontend/notas/controller/informe_stgr_n.php", "frontend/notas/controller/informe_stgr_profesores.php", "frontend/notas/controller/resumen_anual.php"]
endpoints: ["/src/ubis/delegaciones_region_stgr_data"]
capacidades: []
campos: ["form.dl", "post.dl", "post.filtro"]
acciones: ["fnjs_update_div"]
estado_revision: "revisado"
---

# Resumen Anual

Resúmenes estadísticos anuales (legacy `Resumen`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/resumen_anual.php`

## Vistas Relacionadas

- `frontend/notas/view/resumen_anual.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/asig_faltan_que.php`
- `frontend/notas/controller/comprobar_notas.php`
- `frontend/notas/controller/informe_stgr_agd.php`
- `frontend/notas/controller/informe_stgr_n.php`
- `frontend/notas/controller/informe_stgr_profesores.php`
- `frontend/notas/controller/resumen_anual.php`

## Endpoints Usados

- `/src/ubis/delegaciones_region_stgr_data`

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.dl`
- `post.dl`
- `post.filtro`

## Acciones Detectadas

- `fnjs_update_div`

## Ruta de menú

- **Legacy:** vest > actas... > resúmenes
- **Pills2:** vest > actas... > resúmenes

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
