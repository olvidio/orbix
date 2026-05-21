---
id: "asistentes.pantalla.que_ctr_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Que Ctr Lista"
controller: "frontend/asistentes/controller/que_ctr_lista.php"
vistas: ["frontend/asistentes/view/que_ctr_lista.phtml"]
fragmentos_frontend: []
endpoints: ["/src/asistentes/que_ctr_lista_data"]
capacidades: ["asistentes.que_ctr.gestionar"]
campos: ["html.btn_ok", "html.n_agd"]
acciones: ["fnjs_buscar", "fnjs_comprobar_fecha", "fnjs_enviar_formulario", "fnjs_otro"]
estado_revision: "generado"
---

# Que Ctr Lista

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/que_ctr_lista.php`

## Vistas Relacionadas

- `frontend/asistentes/view/que_ctr_lista.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/que_ctr_lista_data`

## Capacidades Relacionadas

- `asistentes.que_ctr.gestionar`

## Campos Detectados

- `html.btn_ok`
- `html.n_agd`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_comprobar_fecha`
- `fnjs_enviar_formulario`
- `fnjs_otro`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
