---
id: "actividadestudios.matriculas_pendientes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matriculas Pendientes"
capacidad: "actividadestudios.matriculas_pendientes.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_pendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas Pendientes

Listado de matrículas pendientes de nota en acta.

## Objetivo De Usuario

El usuario consulta las matrículas que aún no tienen nota definitiva en acta: una fila por
matrícula con actividad, asignatura, alumno y permiso. Puede abrir el dossier de la
actividad o borrar matrículas seleccionadas.

## Punto De Entrada

Pantalla `matriculas_pendientes`
(`frontend/actividadestudios/controller/matriculas_pendientes.php`): al cargar llama a
`matriculas_pendientes_data` (sin parámetros). Refresco vía `fnjs_actualizar`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_pendientes`
- `frontend/dossiers/controller/dossiers_ver.php` (destino de **ver asignaturas ca**)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir **Matr. Pendientes** / **Exam. pendientes de acta** desde el menú.
2. El sistema carga automáticamente `matriculas_pendientes_data`.
3. Se muestra la tabla con avisos de región STGR si aplica.
4. Opcional: ver dossier CA de una fila o borrar matrículas.

Endpoints asociados:
- `/src/actividadestudios/matriculas_pendientes_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.mod`
- `html.pau`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_pendientes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > actas... > Matr. Pendientes.
- **Pills2:** ESTUDIOS > Actas y certificados > Exam. pendientes de acta; ESTUDIOS >
  Preparación planes estudio > Exam. pendientes acta; vest > actas... > Matr. Pendientes.
