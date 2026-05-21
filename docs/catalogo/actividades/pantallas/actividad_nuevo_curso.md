---
id: "actividades.pantalla.actividad_nuevo_curso"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Actividad Nuevo Curso"
controller: "frontend/actividades/controller/actividad_nuevo_curso.php"
vistas: ["frontend/actividades/view/actividad_nuevo_curso.phtml"]
fragmentos_frontend: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
capacidades: ["actividades.actividad_nuevo_curso_ejecutar.gestionar"]
campos: ["form.year", "form.year_ref", "html.ver_lista", "html.year", "html.year_ref", "post.ok", "post.ver_lista", "post.year", "post.year_ref"]
acciones: ["fnjs_enviar_formulario"]
estado_revision: "generado"
---

# Actividad Nuevo Curso

Pantalla que crea las actividades para el nuevo curso, copiando las del curso de referencia.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividades/controller/actividad_nuevo_curso.php`

## Vistas Relacionadas

- `frontend/actividades/view/actividad_nuevo_curso.phtml`

## Fragmentos Frontend Relacionados

- `frontend/actividades/controller/actividad_nuevo_curso.php`

## Endpoints Usados

- `/src/actividades/actividad_nuevo_curso_ejecutar`

## Capacidades Relacionadas

- `actividades.actividad_nuevo_curso_ejecutar.gestionar`

## Campos Detectados

- `form.year`
- `form.year_ref`
- `html.ver_lista`
- `html.year`
- `html.year_ref`
- `post.ok`
- `post.ver_lista`
- `post.year`
- `post.year_ref`

## Acciones Detectadas

- `fnjs_enviar_formulario`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
