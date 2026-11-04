---
id: "actividadestudios.acta_notas_matricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Guardar las notas en borrador"
capacidad: "actividadestudios.acta_notas_matricula.gestionar"
pantallas_principales: ["actividadestudios.pantalla.acta_notas"]
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["guardar"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Notas Matricula

Guardado del borrador de notas sobre cada matrícula del acta.

## Objetivo De Usuario

Editar las notas, la nota máxima, el preceptor o la situación de acta de los alumnos matriculados, y guardarlo como borrador en sus matrículas.

## Punto De Entrada

Pantalla del acta de notas de una asignatura: se guarda al salir de la casilla de nota o al pulsar guardar.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Guardar las notas en borrador

Pasos propuestos:
1. En el acta de notas, cambiar la nota, la nota máxima, el preceptor o la situación del acta de un alumno.
2. Al salir de la casilla de la nota (o al guardar) se graba el cambio en su matrícula.
3. Si algún dato no es válido, sale un aviso con el motivo.

Endpoints asociados:
- `/src/actividadestudios/acta_notas_matricula_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta_nota`
- `form.form_preceptor`
- `form.id_nom`
- `form.nota_max`
- `form.nota_num`
- `html.form_preceptor[]`
- `html.id_nom[]`
- `html.que`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.opcional`
- `post.primary_key_s`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_guardar_nota`
- `fnjs_nota`

## Endpoints Del Flujo

- `/src/actividadestudios/acta_notas_matricula_guardar`

## Errores Conocidos

- ``Hay una nota mayor que el máximo``
- ``hay un error, no se ha guardado``
- ``no se puede definir cursada con preceptor``

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde pantalla `acta_notas`).

- **Legacy:** vest > actas... > actas.
- **Pills2:** ESTUDIOS > Actas y certificados > Actas.
