---
id: "actividadestudios.acta_notas_matricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Acta Notas Matricula"
capacidad: "actividadestudios.acta_notas_matricula.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["guardar"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Notas Matricula

Guardado del borrador de notas sobre cada matrícula del acta.

## Objetivo De Usuario

El usuario edita notas, nota máxima, preceptor o situación de acta de los alumnos
matriculados y guarda el borrador en las matrículas. Sustituye la rama `que=1` del legacy
`apps/actividadestudios/controller/acta_notas_update.php`.

## Punto De Entrada

Pantalla `acta_notas` (`frontend/actividadestudios/controller/acta_notas.php`): `fnjs_guardar_nota`
(disparada al cambiar una nota vía `fnjs_nota`) envía el formulario por AJAX con `que=1`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Guardar

Pasos:
1. En el acta de notas, modificar nota, nota máxima, preceptor o desplegable de acta de un alumno.
2. Al salir del campo nota (`fnjs_nota`) o al guardar explícitamente, se invoca `fnjs_guardar_nota`.
3. El sistema serializa `#f_1303` y llama al endpoint.
4. Si hay error de validación, se muestra alerta con el mensaje devuelto.

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
