---
id: "actividades.pantalla.calendario_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Calendario Listas"
controller: "frontend/actividades/controller/calendario_listas.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/calendario_listas.php"]
endpoints: ["/src/actividades/calendario_listas_datos"]
capacidades: ["actividades.calendario_listas.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.que", "post.ver_ctr", "post.year", "post.yeardefault"]
acciones: []
estado_revision: "generado"
---

# Calendario Listas

Fragmento HTML con el calendario de actividades de casas / oficinas en un periodo dado.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/calendario_listas.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/calendario_listas.php`

## Endpoints Usados

- `/src/actividades/calendario_listas_datos`

## Capacidades Relacionadas

- `actividades.calendario_listas.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.ver_ctr`
- `post.year`
- `post.yeardefault`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
