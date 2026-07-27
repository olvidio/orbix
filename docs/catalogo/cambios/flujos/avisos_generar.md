---
id: "cambios.avisos_generar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Consultar y purgar cambios"
capacidad: "cambios.avisos_generar.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.avisos_generar"]
acciones: ["listar", "eliminar", "eliminar_hasta_fecha", "generar_tabla"]
endpoints: ["/src/cambios/avisos_generar_lista_data", "/src/cambios/avisos_generar_tabla", "/src/cambios/cambio_usuario_eliminar", "/src/cambios/cambio_usuario_eliminar_hasta_fecha"]
estado_revision: "revisado"
---

# Flujo - Consultar y purgar cambios

## Objetivo De Usuario

Ver los cambios registrados pendientes de avisar y eliminar los que ya no interesan (por fila o por
fecha límite). Opcionalmente regenerar la tabla de avisos desde el menú de administración.

## Punto De Entrada

`frontend/cambios/controller/avisos_generar.php` (varias entradas de menú «ver lista cambios»).

## Escenarios

### Generar tabla (batch)

1. Menú «generar tabla avisos» (usuarios web / ADMIN LOCAL) → `/src/cambios/avisos_generar_tabla`
   (también cron / `Cambio::generarTabla()` vía CLI).
2. El sistema cruza cambios nuevos con preferencias y crea avisos pendientes; muestra HTML de
   resultado o incidencias (sin pantalla FE).
3. Revisar el listado en `avisos_generar`.

### Listar

1. Abrir la pantalla.
2. Si es admin, elegir usuario y tipo de aviso.
3. El sistema carga `avisos_generar_lista_data` y muestra la tabla.

### Eliminar filas

1. Marcar filas en la tabla.
2. Pulsar borrar → `cambio_usuario_eliminar` con `sel[]`.

### Eliminar hasta fecha

1. Indicar `f_fin`.
2. Pulsar purga → `cambio_usuario_eliminar_hasta_fecha`.

## Errores Conocidos

- `debe indicar la fecha`
- `Hay un error, no se ha eliminado`
- `Hay un error al eliminar los cambios hasta la fecha indicada`
- Generar tabla: `Algo falla`; HTML de incidencias por cambio no procesado

## Ruta de menú

- **Legacy:** Calendario > actividades > ver lista cambios; sistema > usuarios web > ver lista cambios;
  Utilidades > Utilidades > lista de cambios
- **Pills2:** mismas rutas (+ ADMIN LOCAL en usuarios web)
- Generar tabla: **Legacy/Pills2:** sistema > usuarios web > generar tabla avisos (+ ADMIN LOCAL)
