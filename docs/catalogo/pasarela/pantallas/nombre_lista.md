---
id: "pasarela.pantalla.nombre_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Nombre Lista"
controller: "frontend/pasarela/controller/nombre_lista.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/nombre_ajax.php"]
endpoints: ["/src/", "/src/pasarela/nombre_excepcion_guardar"]
capacidades: ["pasarela.nombre_excepcion.gestionar"]
campos: ["form.id_tipo_activ", "form.nombre_actividad", "form.que", "form.valor"]
acciones: []
estado_revision: "generado"
---

# Nombre Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/nombre_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/nombre_ajax.php`

## Endpoints Usados

- `/src/`
- `/src/pasarela/nombre_excepcion_guardar`

## Capacidades Relacionadas

- `pasarela.nombre_excepcion.gestionar`

## Campos Detectados

- `form.id_tipo_activ`
- `form.nombre_actividad`
- `form.que`
- `form.valor`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
