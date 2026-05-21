---
id: "encargossacd.pantalla.listas_exigencia_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas Exigencia Ctr"
controller: "frontend/encargossacd/controller/listas_exigencia_ctr.php"
vistas: ["frontend/encargossacd/view/listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_exigencia_ctr_data"]
capacidades: ["encargossacd.listas_exigencia_ctr.gestionar"]
campos: ["post.ctr_igl", "post.sf"]
acciones: []
estado_revision: "generado"
---

# Listas Exigencia Ctr

Listado de exigencias de atencion por centros / iglesias (cr 9/05, Anexo2, 9.4 b).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_exigencia_ctr.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_exigencia_ctr_data`

## Capacidades Relacionadas

- `encargossacd.listas_exigencia_ctr.gestionar`

## Campos Detectados

- `post.ctr_igl`
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
