---
id: "ubis.pantalla.calendario_periodos"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "ubis"
nombre: "Calendario Periodos"
controller: "frontend/ubis/controller/calendario_periodos.php"
vistas: ["frontend/ubis/view/calendario_periodos.phtml"]
fragmentos_frontend: ["frontend/ubis/controller/calendario_periodos_form_periodo.php", "frontend/ubis/controller/calendario_periodos_get2.php", "frontend/ubis/controller/calendario_periodos_nuevo.php"]
endpoints: ["/src/ubis/calendario_periodos_eliminar", "/src/ubis/calendario_periodos_guardar"]
capacidades: ["ubis.calendario_periodos.gestionar"]
campos: ["form.id_item", "form.id_ubi", "form.year", "html.buscar"]
acciones: ["fnjs_cerrar", "fnjs_guardar", "fnjs_modificar", "fnjs_update_div", "fnjs_ver"]
estado_revision: "generado"
---

# Calendario Periodos

Esta página sirve para asignar una dirección a un determinado ubi.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/ubis/controller/calendario_periodos.php`

## Vistas Relacionadas

- `frontend/ubis/view/calendario_periodos.phtml`

## Fragmentos Frontend Relacionados

- `frontend/ubis/controller/calendario_periodos_form_periodo.php`
- `frontend/ubis/controller/calendario_periodos_get2.php`
- `frontend/ubis/controller/calendario_periodos_nuevo.php`

## Endpoints Usados

- `/src/ubis/calendario_periodos_eliminar`
- `/src/ubis/calendario_periodos_guardar`

## Capacidades Relacionadas

- `ubis.calendario_periodos.gestionar`

## Campos Detectados

- `form.id_item`
- `form.id_ubi`
- `form.year`
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
