---
id: "ubis.pantalla.calendario_periodos_form_periodo"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Calendario Periodos Form Periodo"
controller: "frontend/ubis/controller/calendario_periodos_form_periodo.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/calendario_periodos_form_periodo_data"]
capacidades: ["ubis.calendario_periodos_form_periodo.gestionar"]
campos: ["form.f_fin", "form.f_ini", "form.sfsv", "post.id_item"]
acciones: ["fnjs_cerrar", "fnjs_guardar"]
estado_revision: "generado"
---

# Calendario Periodos Form Periodo

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/calendario_periodos_form_periodo.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/calendario_periodos_form_periodo_data`

## Capacidades Relacionadas

- `ubis.calendario_periodos_form_periodo.gestionar`

## Campos Detectados

- `form.f_fin`
- `form.f_ini`
- `form.sfsv`
- `post.id_item`

## Acciones Detectadas

- `fnjs_cerrar`
- `fnjs_guardar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
