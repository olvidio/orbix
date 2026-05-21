---
id: "encargossacd.pantalla.listas_c"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Listas C"
controller: "frontend/encargossacd/controller/listas_c.php"
vistas: ["frontend/encargossacd/view/listas.phtml"]
fragmentos_frontend: []
endpoints: ["/src/encargossacd/listas_c_data"]
capacidades: ["encargossacd.listas_c.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Listas C

Listado de atencion SACD segun cr 9/05, Anexo2, 9.4 c).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/listas_c.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/listas.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/encargossacd/listas_c_data`

## Capacidades Relacionadas

- `encargossacd.listas_c.gestionar`

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
