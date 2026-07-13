---
id: "actividadessacd.lista_actividades_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Lista Actividades Sacd"
capacidad: "actividadessacd.lista_actividades_sacd.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/lista_actividades_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Actividades Sacd

Listado de actividades del tipo + periodo elegidos, con sus sacd encargados.

## Objetivo De Usuario

El usuario elige un periodo y pulsa **buscar**: el sistema muestra la tabla de actividades del tipo
(`na` / `sg` / `sr` / `sssc` / `sf` / variantes `sf_*` / `falta_sacd` / `solape`) en ese periodo y,
por cada una, los sacd encargados actuales y los flags de permiso que deciden qué acciones se
ofrecen (asignar, reordenar, borrar).

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): la función `fnjs_ver`
llama a este endpoint al pulsar el botón **buscar**. Si el tipo es `solape`, usa
`solapes_sacd_data` en su lugar.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Elegir periodo (año + trimestre o rango libre) en la barra de filtros.
2. Pulsar **buscar**.
3. El sistema construye la tabla con actividades, sacd encargados y leyenda de colores.

Endpoints asociados:
- `/src/actividadessacd/lista_actividades_sacd_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/lista_actividades_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes: activ sv sg, activ sv sr, activ sv n y
  agd, activ sf sg, activ sf sr, activ sf n,nax y agd, activ sss+, sf).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
