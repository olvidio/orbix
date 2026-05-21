---
id: "encargossacd.pantalla.comprobaciones"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Comprobaciones"
controller: "frontend/encargossacd/controller/comprobaciones.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
capacidades: ["encargossacd.comprobaciones_ctr.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Comprobaciones

Proxy AJAX: la lógica vive en {@see \src\encargossacd\application\EncargoComprobacionesCtr} y se expone en `/src/encargossacd/comprobaciones_ctr`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/comprobaciones.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/comprobaciones_ctr`

## Capacidades Relacionadas

- `encargossacd.comprobaciones_ctr.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
