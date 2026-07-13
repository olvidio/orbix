---
id: "actividades.pantalla.calendario_listas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividades"
nombre: "Listados calendario (casas/oficinas)"
controller: "frontend/actividades/controller/calendario_listas.php"
vistas: []
fragmentos_frontend: ["frontend/actividades/controller/calendario_listas.php"]
endpoints: ["/src/actividades/calendario_listas_datos"]
capacidades: ["actividades.calendario_listas.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_cdc", "post.periodo", "post.que", "post.ver_ctr", "post.year", "post.yeardefault"]
acciones: []
estado_revision: "revisado"
---

# Listados calendario (casas/oficinas)

Fragmento **HTML por AJAX** con listados del *nuevo calendario*: actividades por
oficina, casas comunes, todas las casas, variantes SV/SF, lista de centros
encargados (`que=lista_cdc`), etc. El parámetro `que` determina el informe;
`ver_ctr=si` incluye columna de centros encargados cuando aplica.

También se invoca desde `actividades_centro_que` con `tipo_lista=ctrsEncargados`.

## Tipo

- Subtipo: `fragmento_ajax` (echo directo del HTML del backend)
- Controller: `frontend/actividades/controller/calendario_listas.php`

## Endpoints Usados

- `/src/actividades/calendario_listas_datos` — clave `html`.

Valores habituales de `que`: `o_todas`, `o_actual`, `c_comunes`, `c_todas`,
`c_comunes_sf`, `c_todas_sf`, `c_comunes_sv`, `c_todas_sv`, `lista_cdc`.

## Manual De Usuario

Herramientas de calendario en estudio: elegir tipo de listado y periodo en el menú
padre; el HTML resultante muestra la tabla de actividades agrupadas.

## Ruta de menú

Múltiples entradas según `que` (ejemplos):

- **Legacy:** dre/Calendario/adl > Nuevo calendario > listados > listado por oficinas
  (`o_todas`); casas comunes (`c_comunes`); oficina propia (`o_actual`); todas casas
  sf/sv; etc.
- **Pills2:** ACTIVIDADES > Herramientas de calendario > Activ. nuevo calendario por
  oficina / casas comunes / oficina propia / casas sv (según `que`).
