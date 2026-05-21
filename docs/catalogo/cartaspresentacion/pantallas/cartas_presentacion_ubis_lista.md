---
id: "cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion Ubis Lista"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
capacidades: ["cartaspresentacion.ubis.gestionar"]
campos: ["post.poblacion_sel", "post.tipo_lista"]
acciones: []
estado_revision: "generado"
---

# Cartas Presentacion Ubis Lista

Controlador AJAX HTML: listado de centros con el estado de su carta de presentacion (modal de seleccion de la pantalla principal).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/cartaspresentacion/ubis_lista_data`

## Capacidades Relacionadas

- `cartaspresentacion.ubis.gestionar`

## Campos Detectados

- `post.poblacion_sel`
- `post.tipo_lista`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
