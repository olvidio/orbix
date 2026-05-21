---
id: "encargossacd.pantalla.listas_b"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas B"
controller: "frontend/encargossacd/controller/listas_b.php"
vistas: ["frontend/encargossacd/view/listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_b_data"]
capacidades: ["encargossacd.listas_b.gestionar"]
campos: ["post.sf"]
acciones: []
estado_revision: "generado"
---

# Listas B

Listado de atencion SACD segun cr 9/05, Anexo2, 9.4 b).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_b.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_b_data`

## Capacidades Relacionadas

- `encargossacd.listas_b.gestionar`

## Campos Detectados

- `post.sf`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
