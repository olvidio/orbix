---
id: "profesores.pantalla.profesor_asignatura_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "profesores"
nombre: "Profesor Asignatura Que"
controller: "frontend/profesores/controller/profesor_asignatura_que.php"
vistas: ["frontend/profesores/view/profesor_asignatura_que.phtml"]
fragmentos_frontend: ["frontend/profesores/controller/profesor_asignatura_ajax.php"]
endpoints: ["/src/profesores/profesor_asignatura_que"]
capacidades: ["profesores.profesor_asignatura_que.gestionar"]
campos: ["form.id_asignatura"]
acciones: ["fnjs_left_side_hide", "fnjs_profes"]
estado_revision: "generado"
---

# Profesor Asignatura Que

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/profesores/controller/profesor_asignatura_que.php`

## Vistas Relacionadas

- `frontend/profesores/view/profesor_asignatura_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/profesores/controller/profesor_asignatura_ajax.php`

## Endpoints Usados

- `/src/profesores/profesor_asignatura_que`

## Capacidades Relacionadas

- `profesores.profesor_asignatura_que.gestionar`

## Campos Detectados

- `form.id_asignatura`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_profes`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
