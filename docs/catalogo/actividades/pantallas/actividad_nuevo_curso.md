---
id: "actividades.pantalla.actividad_nuevo_curso"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "actividades"
nombre: "Generar actividades del nuevo curso"
controller: "frontend/actividades/controller/actividad_nuevo_curso.php"
vistas: ["frontend/actividades/view/actividad_nuevo_curso.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividades/actividad_nuevo_curso_ejecutar"]
capacidades: ["actividades.actividad_nuevo_curso_ejecutar.gestionar"]
campos: ["form.year", "form.year_ref", "html.ver_lista", "html.year", "html.year_ref", "post.ok", "post.ver_lista", "post.year", "post.year_ref"]
acciones: ["fnjs_enviar_formulario"]
estado_revision: "revisado"
---

# Generar actividades del nuevo curso

Herramienta de **fin/inicio de curso**: el usuario elige año destino (`year`) y año
de referencia (`year_ref`); al confirmar, POST directo a
`actividad_nuevo_curso_ejecutar`, que borra actividades en proyecto del nuevo curso,
copia las del curso base, opcionalmente centros encargados (`actividadescentro`) y
fases (`procesos`). Muestra avisos de duración (~3 min).

## Tipo

- Subtipo: `pantalla_principal` (formulario completo con advertencias)
- Controller: `frontend/actividades/controller/actividad_nuevo_curso.php`
- Vista: `frontend/actividades/view/actividad_nuevo_curso.phtml`

## Endpoints Usados

- `/src/actividades/actividad_nuevo_curso_ejecutar` — mutación larga; respuesta HTML
  o redirección según `ver_lista`.

## Manual De Usuario

Solo administradores de calendario: leer avisos, elegir años, ejecutar y esperar.
Las actividades nuevas quedan en estado *proyecto*.

## Ruta de menú

- **Legacy:** dre > Nuevo calendario > nuevo curso.
- **Pills2:** dre > Nuevo calendario > nuevo curso; ACTIVIDADES > Herramientas de
  calendario > Generar nuevo curso.
