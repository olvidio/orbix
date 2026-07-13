---
id: "actividadestudios.matricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matricula"
capacidad: "actividadestudios.matricula.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona", "actividadestudios.pantalla.matriculas_lista", "actividadestudios.pantalla.matriculas_pendientes"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matricula_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matricula

Alta y baja de matrículas de alumnos en asignaturas de actividades CA.

## Objetivo De Usuario

El usuario crea una matrícula (persona + asignatura + nivel en una actividad) o elimina
una o varias matrículas seleccionadas desde listados o formularios de dossier.

## Punto De Entrada

- **crear:** `form_matriculas_de_una_persona`, `fnjs_guardar` con modo nuevo →
  `matricula_nueva`.
- **eliminar:** `matriculas_lista`, `matriculas_pendientes` o fragmentos de dossier,
  `fnjs_borrar` → `matricula_eliminar` por AJAX.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`
- `actividadestudios.pantalla.matriculas_lista`
- `actividadestudios.pantalla.matriculas_pendientes`
- `frontend/actividadestudios/view/select_matriculas_de_una_actividad.phtml`

## Escenarios Inferidos

### Crear

Pasos:
1. En dossier 1303 o 3103, pulsar **nuevo** para abrir el formulario de matrícula.
2. Elegir nivel, asignatura y opciones de preceptor.
3. Pulsar **guardar**; el sistema crea la matrícula y actualiza dossiers 1303/3103.

Endpoints asociados:
- `/src/actividadestudios/matricula_nueva`

### Eliminar

Pasos:
1. En un listado de matrículas, seleccionar una o varias filas.
2. Pulsar **borrar matrícula** y confirmar.
3. El sistema elimina las matrículas y refresca el listado.

Endpoints asociados:
- `/src/actividadestudios/matricula_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_nom`
- `form.periodo`
- `form.year`
- `html.id_asignatura`
- `html.mod`
- `html.pau`
- `html.preceptor`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.mod`
- `post.periodo`
- `post.sel`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Endpoints Del Flujo

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matricula_nueva`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha borrado``
- ``hay un error, no se ha guardado``
- ``no encuentro asignatura para ese nivel``
- ``no encuentro la matricula``

## Ruta de menú

Sin entrada de menú directa; las altas se hacen desde dossiers y las bajas también desde
listados de menú:

- **Legacy:** vest > actas... > Matrículas / Matr. Pendientes; vest > buscar persona > n r/dl.
- **Pills2:** ESTUDIOS > Actas y certificados > Matrículas realizadas / Exam. pendientes de acta;
  PERSONAS > Numerarios > Buscar n de la r/dl.
