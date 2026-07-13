---
id: "notas.pantalla.asignaturas_pendientes"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Asignaturas Pendientes"
controller: "frontend/notas/controller/asignaturas_pendientes.php"
vistas: []
fragmentos_frontend: ["frontend/notas/controller/asignaturas_pendientes.php"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
capacidades: ["notas.asignaturas_pendientes.gestionar"]
campos: ["form.dl", "post.dl"]
acciones: ["fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Asignaturas Pendientes

Matriz alumnos × asignaturas pendientes de superar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/asignaturas_pendientes.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/asignaturas_pendientes.php`

## Endpoints Usados

- `/src/notas/asignaturas_pendientes_data`

## Capacidades Relacionadas

- `notas.asignaturas_pendientes.gestionar`

## Campos Detectados

- `form.dl`
- `post.dl`

## Acciones Detectadas

- `fnjs_left_side_hide`

## Ruta de menú

- **Legacy:** vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas
- **Pills2:** ESTUDIOS > Preparación planes estudio > Tab. Alumn/asig.; vest > actas... > tabla alumnos-asignaturas; stgr > actas > tabla alumnos-asignaturas

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
