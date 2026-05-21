---
id: "zonassacd.pantalla.zona_sacd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "zonassacd"
nombre: "Zona Sacd"
controller: "frontend/zonassacd/controller/zona_sacd.php"
vistas: ["frontend/zonassacd/view/zona_sacd.phtml"]
fragmentos_frontend: ["frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
endpoints: ["/src/misas/zona_sacd_datos_get", "/src/misas/zona_sacd_datos_put", "/src/zonassacd/zona_sacd"]
capacidades: ["zonassacd.zona_sacd.gestionar"]
campos: ["form.dw1", "form.dw2", "form.dw3", "form.dw4", "form.dw5", "form.dw6", "form.dw7", "form.id_sacd", "form.id_zona", "form.id_zona_new", "html.dw1", "html.dw2", "html.dw3", "html.dw4", "html.dw5", "html.dw6", "html.dw7", "html.id_zona", "html.id_zona_new", "html.ok", "html.ok2"]
acciones: ["fnjs_busca_sacds", "fnjs_guardar", "fnjs_left_side_hide", "fnjs_modal_zona_sacd_ver", "fnjs_modificar", "fnjs_solo_uno"]
estado_revision: "generado"
---

# Zona Sacd

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/zonassacd/controller/zona_sacd.php`

## Vistas Relacionadas

- `frontend/zonassacd/view/zona_sacd.phtml`

## Fragmentos Frontend Relacionados

- `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`
- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Endpoints Usados

- `/src/misas/zona_sacd_datos_get`
- `/src/misas/zona_sacd_datos_put`
- `/src/zonassacd/zona_sacd`

## Capacidades Relacionadas

- `zonassacd.zona_sacd.gestionar`

## Campos Detectados

- `form.dw1`
- `form.dw2`
- `form.dw3`
- `form.dw4`
- `form.dw5`
- `form.dw6`
- `form.dw7`
- `form.id_sacd`
- `form.id_zona`
- `form.id_zona_new`
- `html.dw1`
- `html.dw2`
- `html.dw3`
- `html.dw4`
- `html.dw5`
- `html.dw6`
- `html.dw7`
- `html.id_zona`
- `html.id_zona_new`
- `html.ok`
- `html.ok2`

## Acciones Detectadas

- `fnjs_busca_sacds`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_modal_zona_sacd_ver`
- `fnjs_modificar`
- `fnjs_solo_uno`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
