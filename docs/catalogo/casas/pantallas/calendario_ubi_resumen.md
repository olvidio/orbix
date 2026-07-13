---
id: "casas.pantalla.calendario_ubi_resumen"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Calendario Ubi Resumen"
controller: "frontend/casas/controller/calendario_ubi_resumen.php"
vistas: ["frontend/casas/view/calendario_ubi_resumen.phtml"]
fragmentos_frontend: ["frontend/casas/controller/calendario_ubi_resumen_body.php"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_update_inc", "/src/casas/calendario_ubi_resumen_data", "/src/ubis/casas_opciones_data"]
capacidades: ["casas.calendario_ubi_resumen.gestionar"]
campos: ["form.G", "form.id_ubi", "form.inc_cantidad", "form.inc_t", "form.seccion", "form.year", "html.G", "html.inc_t", "html.seccion", "post.G", "post.id_ubi", "post.inc_t"]
acciones: ["button:resumen sf", "button:resumen sv", "fnjs_guardar", "fnjs_ver"]
estado_revision: "revisado"
---

# Calendario Ubi Resumen

Pantalla `calendario_ubi_resumen`: estudio económico y de ocupación de una casa.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/calendario_ubi_resumen.php`

## Vistas Relacionadas

- `frontend/casas/view/calendario_ubi_resumen.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/calendario_ubi_resumen_body.php`

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_update_inc`
- `/src/casas/calendario_ubi_resumen_data`
- `/src/ubis/casas_opciones_data`

## Capacidades Relacionadas

- `casas.calendario_ubi_resumen.gestionar`

## Campos Detectados

- `form.G`
- `form.id_ubi`
- `form.inc_cantidad`
- `form.inc_t`
- `form.seccion`
- `form.year`
- `html.G`
- `html.inc_t`
- `html.seccion`
- `post.G`
- `post.id_ubi`
- `post.inc_t`

## Acciones Detectadas

- `button:resumen sf`
- `button:resumen sv`
- `fnjs_guardar`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
## Ruta de menú

- **Legacy:** adl > Nuevo Calendario > Previsión económica
- **Pills2:** ACTIVIDADES > Estadísticas económicas > Previsión económica

