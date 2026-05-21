---
id: "pasarela.pantalla.contribucion_no_duerme_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "pasarela"
nombre: "Contribucion No Duerme Lista"
controller: "frontend/pasarela/controller/contribucion_no_duerme_lista.php"
vistas: []
fragmentos_frontend: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php"]
endpoints: ["/src/", "/src/pasarela/contribucion_no_duerme_excepcion_guardar"]
capacidades: ["pasarela.contribucion_no_duerme_excepcion.gestionar"]
campos: ["form.contribucion", "form.id_tipo_activ", "form.que", "form.valor"]
acciones: []
estado_revision: "generado"
---

# Contribucion No Duerme Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/pasarela/controller/contribucion_no_duerme_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Endpoints Usados

- `/src/`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`

## Capacidades Relacionadas

- `pasarela.contribucion_no_duerme_excepcion.gestionar`

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
