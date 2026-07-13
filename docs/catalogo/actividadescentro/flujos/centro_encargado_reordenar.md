---
id: "actividadescentro.centro_encargado_reordenar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centro Encargado Reordenar"
capacidad: "actividadescentro.centro_encargado_reordenar.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadescentro/centro_encargado_reordenar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centro Encargado Reordenar

Cambio de prioridad de un centro encargado dentro de una actividad.

## Objetivo De Usuario

El usuario sube (**+ prioridad**) o baja (**- prioridad**) un centro encargado en el listado de una
actividad. Internamente se intercambia el `num_orden` con el centro vecino.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función
`fnjs_reordenar` llama a este endpoint desde el popup de orden de un centro.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Pulsar un centro encargado ya asignado para abrir el popup de orden.
2. Elegir **+ prioridad** o **- prioridad**.
3. El sistema reordena y refresca la celda de centros de la actividad.

Endpoints asociados:
- `/src/actividadescentro/centro_encargado_reordenar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centro_encargado_reordenar`

## Errores Conocidos

- ``direccion de orden incorrecta (mas / menos)``
- ``faltan parametros id_activ / id_ubi``

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
