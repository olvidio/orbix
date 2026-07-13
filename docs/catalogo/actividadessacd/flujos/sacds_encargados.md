---
id: "actividadessacd.sacds_encargados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacds Encargados"
capacidad: "actividadessacd.sacds_encargados.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/sacds_encargados_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacds Encargados

Consulta de sacd encargados actuales de una actividad.

## Objetivo De Usuario

Tras asignar, reordenar o borrar un sacd, el sistema refresca la celda de sacd de la actividad
consultando los encargados actuales y los flags de permiso que deciden si se muestran como enlaces
interactivos.

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): la función
`fnjs_actualizar_activ` llama a este endpoint tras cada mutación sobre los sacd de una actividad.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Tras una asignación, reordenación o borrado, el sistema actualiza la celda `<id_activ>_sacds`.
2. Se repintan los sacd encargados con sus enlaces de menú contextual.

Endpoints asociados:
- `/src/actividadessacd/sacds_encargados_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacds_encargados_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes por tipo de actividad).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
