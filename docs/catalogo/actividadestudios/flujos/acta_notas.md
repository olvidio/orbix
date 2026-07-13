---
id: "actividadestudios.acta_notas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Acta Notas"
capacidad: "actividadestudios.acta_notas.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/acta_notas_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta Notas

Carga y visualización del acta de notas de una asignatura concreta de una actividad CA,
con la tabla de alumnos matriculados y sus notas en borrador.

## Objetivo De Usuario

El usuario abre el acta de una asignatura impartida en una actividad: el sistema muestra
el formulario del acta (cabecera vía `acta_ver`) y debajo la lista de matriculados con
nota, nota máxima, preceptor y situación de acta, según permisos de la DL propietaria.

## Punto De Entrada

Pantalla `acta_notas` (`frontend/actividadestudios/controller/acta_notas.php`): al abrirse
llama a `acta_notas_data` con `id_activ` e `id_asignatura`. Se llega desde el listado de
asignaturas del dossier 3005 (`select_asignaturas_de_una_actividad`, acción **actas** /
`fnjs_actas`).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`
- `frontend/notas/controller/acta_ver.php` (cabecera del acta)
- `frontend/notas/controller/acta_imprimir.php` (impresión)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En el dossier de asignaturas de una actividad (3005), seleccionar una asignatura.
2. Pulsar **actas** (`fnjs_actas`).
3. El sistema carga `acta_notas` y consulta `acta_notas_data` con las claves de actividad
   y asignatura.
4. Se muestra el acta con matriculados, desplegable de situaciones y permiso de edición.

Endpoints asociados:
- `/src/actividadestudios/acta_notas_data`

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
- `fnjs_enviar_formulario`
- `fnjs_guardar_nota`
- `fnjs_guardar_tessera`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_nota`

## Endpoints Del Flujo

- `/src/actividadestudios/acta_notas_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde dossier 3005 «asignaturas de una actividad»).
Acceso habitual: buscar actividad CA (`actividad_select`) → dossier asignaturas → **actas**.

Menú de la pantalla padre de búsqueda de actividades:

- **Legacy:** vsm > ca > buscar ca; vest > actas... > actas (vía módulo notas).
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Actas y certificados > Actas;
  ESTUDIOS > Buscar actividades.
