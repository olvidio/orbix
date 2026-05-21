---
id: "casas.pantalla.calendario_ubi_resumen_body"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "casas"
nombre: "Calendario Ubi Resumen Body"
controller: "frontend/casas/controller/calendario_ubi_resumen_body.php"
vistas: ["frontend/casas/view/calendario_ubi_resumen_body.phtml"]
fragmentos_frontend: ["frontend/casas/controller/casa.php"]
endpoints: ["/src/casas/calendario_ubi_resumen_data"]
capacidades: ["casas.calendario_ubi_resumen.gestionar"]
campos: ["html.id_ubi", "html.year", "post.G", "post.id_ubi", "post.inc_t", "post.seccion"]
acciones: ["button:grabar tarifas", "fnjs_guardar", "fnjs_update_div"]
estado_revision: "generado"
---

# Calendario Ubi Resumen Body

Controlador AJAX HTML: cuerpo del informe `calendario_ubi_resumen`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/casas/controller/calendario_ubi_resumen_body.php`

## Vistas Relacionadas

- `frontend/casas/view/calendario_ubi_resumen_body.phtml`

## Fragmentos Frontend Relacionados

- `frontend/casas/controller/casa.php`

## Endpoints Usados

- `/src/casas/calendario_ubi_resumen_data`

## Capacidades Relacionadas

- `casas.calendario_ubi_resumen.gestionar`

## Campos Detectados

- `html.id_ubi`
- `html.year`
- `post.G`
- `post.id_ubi`
- `post.inc_t`
- `post.seccion`

## Acciones Detectadas

- `button:grabar tarifas`
- `fnjs_guardar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
