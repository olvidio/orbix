---
id: "encargossacd.pantalla.listas_index"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "encargossacd"
nombre: "Listas Index"
controller: "frontend/encargossacd/controller/listas_index.php"
vistas: ["frontend/encargossacd/view/listas_index.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/comprobaciones.php", "frontend/encargossacd/controller/listas_cl.php"]
endpoints: []
capacidades: []
campos: ["form.que"]
acciones: ["fnjs_comprobaciones", "fnjs_update_div"]
estado_revision: "generado"
---

# Listas Index

Construye la URL firmada a un controlador de `frontend/encargossacd/controller` con los parametros dados (filtra nulls con `poner_empty_on_null`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/encargossacd/controller/listas_index.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas_index.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/comprobaciones.php`
- `frontend/encargossacd/controller/listas_cl.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.que`

## Acciones Detectadas

- `fnjs_comprobaciones`
- `fnjs_update_div`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
