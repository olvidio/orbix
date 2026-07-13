---
id: "actividadescentro.lista_actividades_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Lista Actividades Ctr"
capacidad: "actividadescentro.lista_actividades_ctr.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/lista_actividades_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Actividades Ctr

Listado de actividades del colectivo + periodo elegidos, con sus centros encargados.

## Objetivo De Usuario

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del
colectivo (`tipo`) en ese periodo y, por cada una, los centros encargados actuales y los flags de
permiso (modificar / crear centros) que deciden qué acciones se ofrecen.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función `fnjs_ver`
llama a este endpoint al pulsar el botón **buscar**.

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

- `/src/actividadescentro/lista_actividades_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
