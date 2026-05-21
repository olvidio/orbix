---
id: "zonassacd.pantalla.zona_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "zonassacd"
nombre: "Zona Ctr"
controller: "frontend/zonassacd/controller/zona_ctr.php"
vistas: ["frontend/zonassacd/view/zona_ctr.phtml"]
fragmentos_frontend: ["frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
endpoints: ["/src/zonassacd/zona_ctr"]
capacidades: ["zonassacd.zona_ctr.gestionar"]
campos: ["form.id_zona", "form.id_zona_new", "html.id_zona_new", "html.ok"]
acciones: ["fnjs_busca_ctrs", "fnjs_guardar", "fnjs_left_side_hide"]
estado_revision: "generado"
---

# Zona Ctr

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/zonassacd/controller/zona_ctr.php`

## Vistas Relacionadas

- `frontend/zonassacd/view/zona_ctr.phtml`

## Fragmentos Frontend Relacionados

- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`
- `frontend/zonassacd/controller/zona_ctr_update_ajax.php`

## Endpoints Usados

- `/src/zonassacd/zona_ctr`

## Capacidades Relacionadas

- `zonassacd.zona_ctr.gestionar`

## Campos Detectados

- `form.id_zona`
- `form.id_zona_new`
- `html.id_zona_new`
- `html.ok`

## Acciones Detectadas

- `fnjs_busca_ctrs`
- `fnjs_guardar`
- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
