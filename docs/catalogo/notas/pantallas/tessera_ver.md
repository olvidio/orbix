---
id: "notas.pantalla.tessera_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Tessera Ver"
controller: "frontend/notas/controller/tessera_ver.php"
vistas: ["frontend/notas/view/tesera_ver.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/tessera_ver_data"]
capacidades: ["notas.tessera_ver.gestionar"]
campos: ["post.sel"]
acciones: ["fnjs_left_side_hide"]
estado_revision: "generado"
---

# Tessera Ver

Tessera de una persona (vista HTML): muestra por cada asignatura del bienio+cuadrienio si esta pendiente, cursada o aprobada, con nota y fecha.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/tessera_ver.php`

## Vistas Relacionadas

- `frontend/notas/view/tesera_ver.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/tessera_ver_data`

## Capacidades Relacionadas

- `notas.tessera_ver.gestionar`

## Campos Detectados

- `post.sel`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
