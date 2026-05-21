---
id: "cartaspresentacion.pantalla.cartas_presentacion_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Lista"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/cartas_presentacion_lista_data"]
capacidades: ["cartaspresentacion.cartas_presentacion.gestionar"]
campos: ["post.dl", "post.pais", "post.poblacion", "post.que", "post.region"]
acciones: []
estado_revision: "generado"
---

# Cartas Presentacion Lista

Pantalla frontend: listado agrupado de cartas de presentacion.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_lista_data`

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion.gestionar`

## Campos Detectados

- `post.dl`
- `post.pais`
- `post.poblacion`
- `post.que`
- `post.region`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
