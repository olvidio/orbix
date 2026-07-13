---
id: "actividadescentro.centro_encargado_asignar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centro Encargado Asignar"
capacidad: "actividadescentro.centro_encargado_asignar.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadescentro/centro_encargado_asignar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centro Encargado Asignar

Asignación de un centro como encargado de una actividad.

## Objetivo De Usuario

El usuario asigna un centro (elegido en el desplegable de candidatos) como encargado de una actividad.
El centro queda al final del listado (`num_orden = max + 1`) con `encargo = 'organizador'`.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función
`fnjs_asignar_ctr` llama a este endpoint al elegir un centro candidato.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. En una actividad, pulsar **nuevo** para ver los centros candidatos.
2. Pulsar el centro deseado.
3. El sistema lo guarda como encargado y refresca la celda de centros de la actividad.

Endpoints asociados:
- `/src/actividadescentro/centro_encargado_asignar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centro_encargado_asignar`

## Errores Conocidos

- ``faltan parametros id_activ / id_ubi``
- ``hay un error, no se ha guardado el centro encargado``

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
