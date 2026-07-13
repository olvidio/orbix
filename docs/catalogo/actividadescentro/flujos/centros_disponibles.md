---
id: "actividadescentro.centros_disponibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centros Disponibles"
capacidad: "actividadescentro.centros_disponibles.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_disponibles_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centros Disponibles

Desplegable de centros candidatos para asignar como encargados de una actividad.

## Objetivo De Usuario

Al pulsar **nuevo** en una actividad, el usuario ve la lista de centros candidatos (filtrada por el
colectivo `tipo`) para elegir cuál asignar como encargado. Para el tipo `sg` la lista incluye, por
centro, el número de actividades en el periodo y la diferencia de días con su actividad más próxima,
para ayudar a repartir la carga.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función
`fnjs_nuevo_ctr` llama a este endpoint al pulsar **nuevo**.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centros_disponibles_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
