---
id: "actividadessacd.comunicacion_activ_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Comunicacion Activ Sacd"
capacidad: "actividadessacd.comunicacion_activ_sacd.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/comunicacion_activ_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Comunicacion Activ Sacd

Construcción del listado de actividades a comunicar a los sacd.

## Objetivo De Usuario

El usuario selecciona un periodo y pulsa **buscar**: el sistema construye, por cada sacd, la lista
de actividades a comunicar (incluidas las de los "sacd de paso" cuando procede) con los textos de la
carta y las cabeceras de columnas.

## Punto De Entrada

Pantalla `com_sacd_activ_periodo` (`frontend/actividadessacd/controller/com_sacd_activ_periodo.php`):
la función `fnjs_ver` llama a este endpoint al pulsar **buscar** (o automáticamente al entrar con
`que=un_sacd` + `sel[]` desde `personas_select`).

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Seleccionar periodo en la barra de filtros (o entrar con un sacd preseleccionado).
2. Pulsar **buscar** (o auto-carga si `AUTO_CARGAR`).
3. El sistema pinta el listado por sacd con actividades, textos y leyenda.

Endpoints asociados:
- `/src/actividadessacd/comunicacion_activ_sacd_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/comunicacion_activ_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `com_sacd_activ_periodo`:

- **Legacy:** dre > actividades > comunic. sacd · exterior > sacd > atención actividades
- **Pills2:** ATENCIÓN SACD > Actividades > Comunicación a los sacd

Con `propuesta=true`: dre > propuestas > lista activ. sacd.
