---
id: "actividadessacd.sacd_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd Asignar"
capacidad: "actividadessacd.sacd_asignar.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_asignar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Asignar

Asignación de un sacd a una actividad.

## Objetivo De Usuario

El usuario asigna un sacd candidato (elegido en el desplegable de disponibles) a una actividad. El
sacd queda en el primer hueco libre de cargos tipo `sacd`. Si la actividad es de sv
(`id_tipo_activ` empieza por `1`), se crea además la fila de asistencia.

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): la función
`fnjs_asignar_sacd` llama a este endpoint al elegir un sacd del popup de candidatos.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En una actividad con permiso, pulsar **nuevo** para ver los sacd candidatos.
2. Pulsar el sacd deseado (titular del centro o global según checkboxes de selección).
3. El sistema lo guarda como encargado y refresca la celda de sacd de la actividad.

Endpoints asociados:
- `/src/actividadessacd/sacd_asignar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_asignar`

## Errores Conocidos

- ``No puede haber tantos cargos de sacd en una actividad``
- ``faltan parametros id_activ / id_nom``
- ``hay un error, no se ha guardado el cargo``
- ``hay un error, no se ha guardado la asistencia``

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes por tipo de actividad).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
