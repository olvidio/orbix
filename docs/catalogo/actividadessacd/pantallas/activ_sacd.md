---
id: "actividadessacd.pantalla.activ_sacd"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadessacd"
nombre: "Activ Sacd"
controller: "frontend/actividadessacd/controller/activ_sacd.php"
vistas: ["frontend/actividadessacd/view/activ_sacd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadessacd/", "/src/actividadessacd/lista_actividades_sacd_data", "/src/actividadessacd/sacd_asignar", "/src/actividadessacd/sacd_eliminar", "/src/actividadessacd/sacd_reordenar", "/src/actividadessacd/sacds_disponibles_data", "/src/actividadessacd/sacds_encargados_data", "/src/actividadessacd/solapes_sacd_data"]
capacidades: ["actividadessacd.lista_actividades_sacd.gestionar", "actividadessacd.sacd.gestionar", "actividadessacd.sacd_asignar.gestionar", "actividadessacd.sacd_reordenar.gestionar", "actividadessacd.sacds_disponibles.gestionar", "actividadessacd.sacds_encargados.gestionar", "actividadessacd.solapes_sacd.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.periodo", "form.tipo", "form.year", "post.periodo", "post.tipo", "post.year"]
acciones: ["fnjs_actualizar_activ", "fnjs_asignar_sacd", "fnjs_cambiar_sacd", "fnjs_cerrar", "fnjs_construir_celda_sacds", "fnjs_construir_leyenda", "fnjs_construir_tabla_disponibles", "fnjs_construir_tabla_lista", "fnjs_construir_tabla_solapes", "fnjs_enviar", "fnjs_esc", "fnjs_left_side_hide", "fnjs_nuevo_sacd", "fnjs_orden", "fnjs_parse_rta", "fnjs_ver"]
estado_revision: "revisado"
---

# Activ Sacd

Pantalla principal del módulo `actividadessacd`: "Asignar sacd a actividades". Lista las actividades
del tipo elegido en el menú (`na` / `sg` / `sr` / `sssc` / `sf` / `sf_na` / `sf_sg` / `sf_sr` /
`falta_sacd` / `solape`) y, por cada una, sus sacd encargados, permitiendo asignar, reordenar y
borrar. El controller solo pinta la barra de filtros (periodo) y contenedores vacíos; el listado y
las mutaciones se cargan por AJAX contra los endpoints `/src/actividadessacd/*` (URLs firmadas con
`HashFront`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadessacd/controller/activ_sacd.php`

## Vistas Relacionadas

- `frontend/actividadessacd/view/activ_sacd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadessacd/`
- `/src/actividadessacd/lista_actividades_sacd_data`
- `/src/actividadessacd/sacd_asignar`
- `/src/actividadessacd/sacd_eliminar`
- `/src/actividadessacd/sacd_reordenar`
- `/src/actividadessacd/sacds_disponibles_data`
- `/src/actividadessacd/sacds_encargados_data`
- `/src/actividadessacd/solapes_sacd_data`

## Capacidades Relacionadas

- `actividadessacd.lista_actividades_sacd.gestionar`
- `actividadessacd.sacd.gestionar`
- `actividadessacd.sacd_asignar.gestionar`
- `actividadessacd.sacd_reordenar.gestionar`
- `actividadessacd.sacds_disponibles.gestionar`
- `actividadessacd.sacds_encargados.gestionar`
- `actividadessacd.solapes_sacd.gestionar`

## Campos Detectados

- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

## Acciones Detectadas

- `fnjs_actualizar_activ`
- `fnjs_asignar_sacd`
- `fnjs_cambiar_sacd`
- `fnjs_cerrar`
- `fnjs_construir_celda_sacds`
- `fnjs_construir_leyenda`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_construir_tabla_solapes`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_sacd`
- `fnjs_orden`
- `fnjs_parse_rta`
- `fnjs_ver`

## Manual De Usuario

1. Elegir el periodo (año + trimestre o rango libre) en la barra de filtros y pulsar **buscar**.
2. La tabla muestra, por actividad, los sacd encargados. Los colores siguen la leyenda (proyecto,
   aprobada, con fase). El tipo `solape` muestra los sacd con actividades incompatibles.
3. Con permiso, en la columna **nuevo** se abre el desplegable de sacd candidatos (titulares del
   centro + globales según los checkboxes de selección) para asignar uno.
4. Al pulsar un sacd ya asignado se abre el menú para subir/bajar prioridad o borrarlo.
5. En sv, al asignar un sacd se añade también la asistencia a la actividad.

## Ruta de menú

- **Legacy:** dre > propuestas > asignar sacd
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades

Variantes por tipo de actividad (parámetro `tipo`): p. ej. `activ sv sg` (`tipo=sg&periodo=desdeHoy`),
`activ sv sr` (`tipo=sr`), `activ sv n y agd` (`tipo=na`), `activ sf sg` (`tipo=sf_sg`),
`activ sf sr` (`tipo=sf_sr`), `activ sf n,nax y agd` (`tipo=sf_na`), `activ sss+` (`tipo=sssc`),
`sf` (`tipo=sf`), colgando de la misma entrada.
