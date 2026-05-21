---
id: "pasarela.pantalla.contribucion_reserva_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Contribucion Reserva Lista"
controller: "frontend/pasarela/controller/contribucion_reserva_lista.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
endpoints: ["/src/pasarela/contribucion_reserva_excepcion_guardar"]
capacidades: ["pasarela.contribucion_reserva_excepcion.gestionar"]
campos: ["form.contribucion", "form.id_tipo_activ", "form.que", "form.valor"]
acciones: []
estado_revision: "generado"
---

# Contribucion Reserva Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/contribucion_reserva_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Endpoints Usados

- `/src/pasarela/contribucion_reserva_excepcion_guardar`

## Capacidades Relacionadas

- `pasarela.contribucion_reserva_excepcion.gestionar`

## Campos Detectados

- `form.contribucion`
- `form.id_tipo_activ`
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
