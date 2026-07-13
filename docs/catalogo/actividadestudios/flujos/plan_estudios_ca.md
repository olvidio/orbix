---
id: "actividadestudios.plan_estudios_ca.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Plan Estudios Ca"
capacidad: "actividadestudios.plan_estudios_ca.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.plan_estudios_ca"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/plan_estudios_ca_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Plan Estudios Ca

Informe del plan de estudios de una actividad CA.

## Objetivo De Usuario

El usuario consulta el plan de estudios de una actividad CA: director de estudios,
preceptores, profesores por asignatura y alumnos con sus asignaturas matriculadas y
observaciones de plan (`observ_est`).

## Punto De Entrada

Pantalla `plan_estudios_ca` (`frontend/actividadestudios/controller/plan_estudios_ca.php`):
se abre desde `actividad_select` con acción `plan_estudios`; llama a
`plan_estudios_ca_data` con `id_activ`.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.plan_estudios_ca`
- `frontend/actividades/controller/actividad_select.php` (pantalla padre)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En `actividad_select`, seleccionar una actividad CA.
2. Pulsar la acción **plan estudios**.
3. El sistema consulta `plan_estudios_ca_data` y muestra el informe completo.

Endpoints asociados:
- `/src/actividadestudios/plan_estudios_ca_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/plan_estudios_ca_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde `actividad_select`:

- **Legacy:** vsm > ca > buscar ca; vest > sem inv. > buscar.
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n; ESTUDIOS > Buscar actividades;
  ESTUDIOS > Semestres de invierno > Buscar.
