---
id: "encargossacd.pantalla.listas_com_sacd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas Com Sacd"
controller: "frontend/encargossacd/controller/listas_com_sacd.php"
vistas: ["frontend/encargossacd/view/listas_com_sacd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_com_sacd_data"]
capacidades: ["encargossacd.listas_com_sacd.gestionar"]
campos: ["post.sel"]
acciones: []
estado_revision: "generado"
---

# Listas Com Sacd

Comunicacion a los SACD.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_com_sacd.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas_com_sacd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_com_sacd_data`

## Capacidades Relacionadas

- `encargossacd.listas_com_sacd.gestionar`

## Campos Detectados

- `post.sel`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
