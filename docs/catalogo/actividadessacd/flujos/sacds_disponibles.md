---
id: "actividadessacd.sacds_disponibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacds Disponibles"
capacidad: "actividadessacd.sacds_disponibles.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/sacds_disponibles_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacds Disponibles

Consulta de sacd candidatos para asignar a una actividad.

## Objetivo De Usuario

Antes de asignar un sacd, el usuario abre el popup de candidatos: el sistema devuelve los sacd del
centro encargado (titulares) y los sacd globales según el bitmask de selección (`sel`) activo en la
barra de filtros.

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): la función
`fnjs_nuevo_sacd` llama a este endpoint al pulsar **nuevo** en una actividad.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En una actividad con permiso, pulsar **nuevo**.
2. El sistema muestra el popup con sacd titulares del centro y globales filtrados.

Endpoints asociados:
- `/src/actividadessacd/sacds_disponibles_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacds_disponibles_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_sacd` (tipo según parámetro `tipo`):

- **Legacy:** dre > propuestas > asignar sacd (variantes por tipo de actividad).
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades (mismas variantes por `tipo`).
