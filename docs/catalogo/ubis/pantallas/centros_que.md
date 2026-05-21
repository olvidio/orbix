---
id: "ubis.pantalla.centros_que"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "ubis"
nombre: "Centros Que"
controller: "frontend/ubis/controller/centros_que.php"
vistas: ["frontend/ubis/view/centros_que.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/centros_form_labor.php", "frontend/ubis/controller/centros_form_num.php", "frontend/ubis/controller/centros_form_plazas.php", "frontend/ubis/controller/centros_get_labor.php", "frontend/ubis/controller/centros_get_num.php", "frontend/ubis/controller/centros_get_plazas.php"]
endpoints: ["/src/ubis/centros_update"]
capacidades: ["ubis.centros.gestionar"]
campos: ["form.id_ubi", "form.que", "html.buscar"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Centros Que

Esta página sirve para asignar una dirección a un determinado ubi.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/ubis/controller/centros_que.php`

## Vistas Relacionadas

- `frontend/ubis/view/centros_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/centros_form_labor.php`
- `frontend/ubis/controller/centros_form_num.php`
- `frontend/ubis/controller/centros_form_plazas.php`
- `frontend/ubis/controller/centros_get_labor.php`
- `frontend/ubis/controller/centros_get_num.php`
- `frontend/ubis/controller/centros_get_plazas.php`

## Endpoints Usados

- `/src/ubis/centros_update`

## Capacidades Relacionadas

- `ubis.centros.gestionar`

## Campos Detectados

- `form.id_ubi`
- `form.que`
- `html.buscar`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
