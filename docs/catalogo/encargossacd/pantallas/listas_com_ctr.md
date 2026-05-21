---
id: "encargossacd.pantalla.listas_com_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas Com Ctr"
controller: "frontend/encargossacd/controller/listas_com_ctr.php"
vistas: ["frontend/encargossacd/view/listas_com_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_com_ctr_data"]
capacidades: ["encargossacd.listas_com_ctr.gestionar"]
campos: ["post.sfsv"]
acciones: []
estado_revision: "generado"
---

# Listas Com Ctr

Comunicacion para los centros (ficha de atencion SACD).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_com_ctr.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas_com_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_com_ctr_data`

## Capacidades Relacionadas

- `encargossacd.listas_com_ctr.gestionar`

## Campos Detectados

- `post.sfsv`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
