---
id: "cartaspresentacion.pantalla.cartas_presentacion_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Form"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_form.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion_form.phtml"]
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/carta_presentacion_form_data"]
capacidades: ["cartaspresentacion.carta_presentacion.gestionar"]
campos: ["html.observ", "html.pres_mail", "html.pres_nom", "html.pres_telf", "html.zona", "post.id_direccion", "post.id_ubi"]
acciones: ["fnjs_cerrar", "fnjs_guardar_cp"]
estado_revision: "generado"
---

# Cartas Presentacion Form

Controlador AJAX HTML: formulario modal de modificacion de una `CartaPresentacion`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_form.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion_form.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cartaspresentacion/carta_presentacion_form_data`

## Capacidades Relacionadas

- `cartaspresentacion.carta_presentacion.gestionar`

## Campos Detectados

- `html.observ`
- `html.pres_mail`
- `html.pres_nom`
- `html.pres_telf`
- `html.zona`
- `post.id_direccion`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar_cp`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
