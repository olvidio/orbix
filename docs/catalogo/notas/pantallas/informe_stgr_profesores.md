---
id: "notas.pantalla.informe_stgr_profesores"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Informe Stgr Profesores"
controller: "frontend/notas/controller/informe_stgr_profesores.php"
vistas: ["frontend/notas/view/informe_stgr_tabla.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/informe_stgr_profesores_data"]
capacidades: ["notas.informe_stgr_profesores.gestionar"]
campos: ["post.lista"]
acciones: []
estado_revision: "generado"
---

# Informe Stgr Profesores

Informe anual STGR - Profesores (puntos 36..47).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/informe_stgr_profesores.php`

## Vistas Relacionadas

- `frontend/notas/view/informe_stgr_tabla.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/informe_stgr_profesores_data`

## Capacidades Relacionadas

- `notas.informe_stgr_profesores.gestionar`

## Campos Detectados

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
