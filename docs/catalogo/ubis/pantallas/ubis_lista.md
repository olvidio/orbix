---
id: "ubis.pantalla.ubis_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "ubis"
nombre: "Ubis Lista"
controller: "frontend/ubis/controller/ubis_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/ubis/ubis_lista_data"]
capacidades: ["ubis.ubis.gestionar"]
campos: ["post.nombre_ubi"]
acciones: ["fnjs_buscar"]
estado_revision: "generado"
---

# Ubis Lista

Esta página muestra una tabla con los ubis seleccionados.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/ubis/controller/ubis_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/ubis/ubis_lista_data`

## Capacidades Relacionadas

- `ubis.ubis.gestionar`

## Campos Detectados

- `post.nombre_ubi`

## Acciones Detectadas

- `fnjs_buscar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
