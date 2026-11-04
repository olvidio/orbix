---
id: "actividadescentro.centros_encargados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Ver los centros encargados actualizados"
capacidad: "actividadescentro.centros_encargados.gestionar"
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_encargados_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centros Encargados

Recarga de la celda de centros encargados de una actividad tras una mutación.

## Objetivo De Usuario

Refrescar la lista de centros encargados de una actividad tras asignar, reordenar o quitar uno.

## Punto De Entrada

Automático: no requiere acción del usuario.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Ver los centros encargados actualizados

Pasos propuestos:
1. Asignar, reordenar o quitar un centro.
2. La celda de la actividad se refresca sola con la lista al día.

Endpoints asociados:
- `/src/actividadescentro/centros_encargados_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centros_encargados_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
