---
id: "actividadessacd.sacd_reordenar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd Reordenar"
capacidad: "actividadessacd.sacd_reordenar.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_reordenar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Reordenar

Reordenación de la prioridad de un sacd dentro de una actividad.

## Objetivo De Usuario

El usuario sube o baja la prioridad de un sacd ya asignado intercambiando su posición con el
anterior o el siguiente en el listado de cargos `sacd` de la actividad.

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): al pulsar un sacd
asignado se abre el menú contextual; **más prioridad** / **menos prioridad** llaman a
`fnjs_orden(..., 'mas'|'menos')`, que invoca este endpoint. El parámetro `num_orden` es la
dirección (`mas` / `menos`), no un número ordinal.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En una actividad, pulsar un sacd ya asignado.
2. Elegir **más prioridad** o **menos prioridad**.
3. El sistema intercambia el orden y refresca la celda de sacd de la actividad.

Endpoints asociados:
- `/src/actividadessacd/sacd_reordenar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_reordenar`

## Errores Conocidos

- ``direccion de orden incorrecta (mas / menos)``
- ``faltan parametros id_activ / id_nom``
- ``hay un error, no se ha guardado``

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes por tipo de actividad).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
