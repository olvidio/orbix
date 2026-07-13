---
id: "actividadescentro.centro_encargado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centro Encargado"
capacidad: "actividadescentro.centro_encargado.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["eliminar"]
endpoints: ["/src/actividadescentro/centro_encargado_eliminar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centro Encargado

Eliminación de un centro encargado de una actividad.

## Objetivo De Usuario

El usuario quita un centro de la lista de encargados de una actividad.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función
`fnjs_eliminar` llama a este endpoint desde la opción **borrar** del popup de orden.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Eliminar

Pasos:
1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **borrar**.
3. El sistema lo elimina y refresca la celda de centros de la actividad.

Endpoints asociados:
- `/src/actividadescentro/centro_encargado_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centro_encargado_eliminar`

## Errores Conocidos

- ``el centro encargado ya no existe``
- ``hay un error, no se ha eliminado el centro``
- ``no se sabe cual borrar``

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
