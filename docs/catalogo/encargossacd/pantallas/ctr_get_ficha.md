---
id: "encargossacd.pantalla.ctr_get_ficha"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Ctr Get Ficha"
controller: "frontend/encargossacd/controller/ctr_get_ficha.php"
vistas: ["frontend/encargossacd/view/ctr_get_ficha.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/ctr_get_ficha.php", "frontend/encargossacd/controller/horario_ver.php"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
capacidades: ["encargossacd.ctr_get_ficha.gestionar"]
campos: ["form.id_ubi", "form.seleccion_sacd", "html.ok", "post.id_ubi", "post.seleccion_sacd"]
acciones: ["fnjs_cambiar_lista_sacd", "fnjs_cerrar", "fnjs_crear_horario", "fnjs_guardar", "fnjs_update_div"]
estado_revision: "generado"
---

# Ctr Get Ficha

Ficha de atencion sacerdotal de un centro.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/ctr_get_ficha.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/ctr_get_ficha.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/ctr_get_ficha.php`
- `frontend/encargossacd/controller/horario_ver.php`

## Endpoints Usados

- `/src/encargossacd/ctr_get_ficha_data`

## Capacidades Relacionadas

- `encargossacd.ctr_get_ficha.gestionar`

## Campos Detectados

- `form.id_ubi`
- `form.seleccion_sacd`
- `html.ok`
- `post.id_ubi`
- `post.seleccion_sacd`

## Acciones Detectadas

- `fnjs_cambiar_lista_sacd`
- `fnjs_cerrar`
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
