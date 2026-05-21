---
id: "cartaspresentacion.pantalla.cartas_presentacion_buscar"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Buscar"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion_buscar.phtml"]
fragmentos_frontend: ["frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data"]
capacidades: ["cartaspresentacion.cartas_presentacion_buscar.gestionar"]
campos: ["html.btn_ok", "html.poblacion"]
acciones: ["fnjs_buscar", "fnjs_enviar", "fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Cartas Presentacion Buscar

Pantalla frontend: formulario de busqueda de cartas de presentacion.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_buscar.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion_buscar.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion_buscar.gestionar`

## Campos Detectados

- `html.btn_ok`
- `html.poblacion`

## Acciones Detectadas

- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
