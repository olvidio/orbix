---
id: "encargossacd.pantalla.listas_a"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas A"
controller: "frontend/encargossacd/controller/listas_a.php"
vistas: ["frontend/encargossacd/view/listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_a_data"]
capacidades: ["encargossacd.listas_a.gestionar"]
campos: ["post.sf"]
acciones: []
estado_revision: "generado"
---

# Listas A

Listado de atencion SACD segun cr 9/05, Anexo2, 9.4 a).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_a.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_a_data`

## Capacidades Relacionadas

- `encargossacd.listas_a.gestionar`

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
