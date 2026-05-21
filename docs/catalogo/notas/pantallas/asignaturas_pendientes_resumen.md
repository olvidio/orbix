---
id: "notas.pantalla.asignaturas_pendientes_resumen"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Asignaturas Pendientes Resumen"
controller: "frontend/notas/controller/asignaturas_pendientes_resumen.php"
vistas: ["frontend/notas/view/asignaturas_pendientes_resumen.phtml"]
fragmentos_frontend: []
endpoints: ["/src/notas/asignaturas_pendientes_resumen_data"]
capacidades: ["notas.asignaturas_pendientes_resumen.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Asignaturas Pendientes Resumen

Esta página sirve para generar un cuadro con el numero de alumnos que tienen pendiente cada asignatura.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/asignaturas_pendientes_resumen.php`

## Vistas Relacionadas

- `frontend/notas/view/asignaturas_pendientes_resumen.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/asignaturas_pendientes_resumen_data`

## Capacidades Relacionadas

- `notas.asignaturas_pendientes_resumen.gestionar`

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
