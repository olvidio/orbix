---
id: "actividadestudios.matriculas_pendientes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Ver y borrar matrículas pendientes"
capacidad: "actividadestudios.matriculas_pendientes.gestionar"
pantallas_principales: ["actividadestudios.pantalla.matriculas_pendientes"]
fragmentos: ["actividadestudios.pantalla.matriculas_pendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Matriculas Pendientes

Listado de matrículas pendientes de nota en acta.

## Objetivo De Usuario

Ver las matrículas de actividades pendientes de procesar y borrar las que no valgan.

## Punto De Entrada

Menú **vest > actas... > Matr. Pendientes** (Pills2: ESTUDIOS > Actas y certificados > Exam. pendientes de acta).

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_pendientes`
- `frontend/dossiers/controller/dossiers_ver.php` (destino de **ver asignaturas ca**)

## Escenarios Inferidos

### Ver y borrar matrículas pendientes

Pasos propuestos:
1. Entrar en **Matr. Pendientes**: aparece una lista de matrículas con casilla para marcar.
2. Marcar las matrículas y pulsar **ver asignaturas ca** (ver sus asignaturas) o **borrar matrícula**.
3. Si hay datos huérfanos, salen mensajes como «No encuentro a nadie con id_nom: …» o «No encuentro ninguna actividad con id: … (alumno id_nom: …)».

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
