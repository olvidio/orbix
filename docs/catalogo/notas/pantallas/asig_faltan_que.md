---
id: "notas.pantalla.asig_faltan_que"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Asig Faltan Que"
controller: "frontend/notas/controller/asig_faltan_que.php"
vistas: ["frontend/notas/view/asig_faltan_que.phtml"]
fragmentos_frontend: ["frontend/notas/controller/asig_faltan_personas_select.php", "frontend/notas/controller/asig_faltan_select.php"]
endpoints: ["/src/asignaturas/asignaturas_con_separador_data"]
capacidades: []
campos: ["form.b_c", "form.id_asignatura", "form.numero", "html.b_c", "html.btn_ok", "html.c1", "html.c2", "html.lista", "html.numero", "html.personas_agd", "html.personas_n", "post.b_c", "post.c1", "post.c2", "post.id_asignatura", "post.lista", "post.numero", "post.personas_agd", "post.personas_n", "post.stack"]
acciones: ["fnjs_enviar", "fnjs_enviar_formulario", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Asig Faltan Que

Filtro para buscar alumnos con asignaturas pendientes (por número o por asignatura).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/asig_faltan_que.php`

## Vistas Relacionadas

- `frontend/notas/view/asig_faltan_que.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/asig_faltan_personas_select.php`
- `frontend/notas/controller/asig_faltan_select.php`

## Endpoints Usados

- `/src/asignaturas/asignaturas_con_separador_data`

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `form.b_c`
- `form.id_asignatura`
- `form.numero`
- `html.b_c`
- `html.btn_ok`
- `html.c1`
- `html.c2`
- `html.lista`
- `html.numero`
- `html.personas_agd`
- `html.personas_n`
- `post.b_c`
- `post.c1`
- `post.c2`
- `post.id_asignatura`
- `post.lista`
- `post.numero`
- `post.personas_agd`
- `post.personas_n`
- `post.stack`

## Acciones Detectadas

- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Ruta de menú

- **Legacy:** vest > actas... > buscar asig. pendientes
- **Pills2:** ESTUDIOS > Preparación planes estudio > Filtro alumno/asignatura; vest > actas... > buscar asig. pendientes

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
