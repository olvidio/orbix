---
id: "actividadescentro.centros_disponibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Asignar un centro encargado"
capacidad: "actividadescentro.centros_disponibles.gestionar"
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_disponibles_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centros Disponibles

Desplegable de centros candidatos para asignar como encargados de una actividad.

## Objetivo De Usuario

Ver los centros candidatos para asignar como encargados de una actividad.

## Punto De Entrada

Pantalla de asignación de centros encargados, botón **nuevo**.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Asignar un centro encargado

Pasos propuestos:
1. En la fila de la actividad, pulsar **nuevo**.
2. Se listan los centros candidatos (para sg, con el nº de actividades del periodo y la distancia en días a su actividad más próxima).
3. Elegir el centro para asignarlo.

Endpoints asociados:
- `/src/actividadescentro/centros_disponibles_data`

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
