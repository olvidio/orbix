---
id: "actividadestudios.docencia_actualizar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Docencia Actualizar"
capacidad: "actividadestudios.docencia_actualizar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.actualizar_docencia"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/docencia_actualizar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Docencia Actualizar

Actualización del dossier de docencia STGR a partir de actividades terminadas.

## Objetivo De Usuario

El usuario elige un periodo de actividades terminadas y ejecuta la actualización: el sistema
recorre las asignaturas con profesor asignado y graba/actualiza registros en `d_docencia_stgr`
(`ProfesorDocenciaStgr`). Sustituye la rama «continuar» del legacy
`apps/actividadestudios/controller/actualizar_docencia.php`.

## Punto De Entrada

Pantalla `actualizar_docencia` (`frontend/actividadestudios/controller/actualizar_docencia.php`):
primero muestra el formulario de periodo; al pulsar **buscar** con `continuar=1` llama a
`docencia_actualizar` vía PostRequest.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.actualizar_docencia`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Abrir **actualizar docencia** desde el menú.
2. Elegir año y periodo (o fechas personalizadas) y pulsar **buscar**.
3. El sistema calcula la docencia de actividades terminadas en el rango y la persiste.
4. Se muestra el mensaje de resultado en la misma pantalla.

Endpoints asociados:
- `/src/actividadestudios/docencia_actualizar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.continuar`
- `post.empiezamax`
- `post.empiezamin`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadestudios/docencia_actualizar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > mantenimiento > actualizar docencia.
- **Pills2:** ESTUDIOS > Datos e informes > Actualizar docencia; vest > mantenimiento >
  actualizar docencia.
