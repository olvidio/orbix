---
id: "notas.pantalla.informe_stgr_n"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Informe Stgr N"
controller: "frontend/notas/controller/informe_stgr_n.php"
vistas: ["frontend/notas/view/informe_stgr_tabla.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/informe_stgr_n_data"]
capacidades: ["notas.informe_stgr_n.gestionar"]
campos: ["post.dl", "post.lista"]
acciones: []
estado_revision: "generado"
---

# Informe Stgr N

Informe anual STGR - Numerarios (puntos 1..18 + `x`).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/informe_stgr_n.php`

## Vistas Relacionadas

- `frontend/notas/view/informe_stgr_tabla.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/informe_stgr_n_data`

## Capacidades Relacionadas

- `notas.informe_stgr_n.gestionar`

## Campos Detectados

- `post.dl`
- `post.lista`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
