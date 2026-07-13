---
id: "actividadescentro.pantalla.activ_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadescentro"
nombre: "Activ Ctr"
controller: "frontend/actividadescentro/controller/activ_ctr.php"
vistas: ["frontend/actividadescentro/view/activ_ctr.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadescentro/activ_ctr_shell_data", "/src/actividadescentro/lista_actividades_ctr_data", "/src/actividadescentro/centros_encargados_data", "/src/actividadescentro/centros_disponibles_data", "/src/actividadescentro/centro_encargado_asignar", "/src/actividadescentro/centro_encargado_reordenar", "/src/actividadescentro/centro_encargado_eliminar"]
capacidades: ["actividadescentro.activ_ctr_shell.gestionar"]
campos: ["form.empiezamax", "form.empiezamin", "form.periodo", "form.tipo", "form.year", "post.periodo", "post.tipo", "post.year"]
acciones: ["fnjs_actualizar_activ", "fnjs_asignar_ctr", "fnjs_cambiar_ctr", "fnjs_cerrar", "fnjs_construir_celda_ctrs", "fnjs_construir_tabla_disponibles", "fnjs_construir_tabla_lista", "fnjs_eliminar", "fnjs_enviar", "fnjs_esc", "fnjs_left_side_hide", "fnjs_nuevo_ctr", "fnjs_parse_rta", "fnjs_reordenar", "fnjs_ver"]
estado_revision: "revisado"
---

# Activ Ctr

Pantalla principal del módulo `actividadescentro`: lista las actividades de un colectivo (`tipo`) en
un periodo y permite gestionar, por actividad, los **centros encargados** (asignar, reordenar,
eliminar).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadescentro/controller/activ_ctr.php`

## Vistas Relacionadas

- `frontend/actividadescentro/view/activ_ctr.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadescentro/activ_ctr_shell_data` (bootstrap: resuelve `tipo` y firma las demás URLs)
- `/src/actividadescentro/lista_actividades_ctr_data` (`fnjs_ver`)
- `/src/actividadescentro/centros_encargados_data` (`fnjs_actualizar_activ`)
- `/src/actividadescentro/centros_disponibles_data` (`fnjs_nuevo_ctr`)
- `/src/actividadescentro/centro_encargado_asignar` (`fnjs_asignar_ctr`)
- `/src/actividadescentro/centro_encargado_reordenar` (`fnjs_reordenar`)
- `/src/actividadescentro/centro_encargado_eliminar` (`fnjs_eliminar`)

## Capacidades Relacionadas

- `actividadescentro.activ_ctr_shell.gestionar`

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
- `fnjs_asignar_ctr`
- `fnjs_cambiar_ctr`
- `fnjs_cerrar`
- `fnjs_construir_celda_ctrs`
- `fnjs_construir_tabla_disponibles`
- `fnjs_construir_tabla_lista`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_esc`
- `fnjs_left_side_hide`
- `fnjs_nuevo_ctr`
- `fnjs_parse_rta`
- `fnjs_reordenar`
- `fnjs_ver`

## Manual De Usuario

La pantalla solo pinta la barra de filtros de periodo y contenedores vacíos; el listado y todas las
mutaciones se cargan por AJAX contra los endpoints `/src/actividadescentro/*`. El HTML de la tabla y
de los desplegables se construye en cliente a partir del JSON.

Flujo habitual:

1. El controller carga `activ_ctr_shell_data` para resolver el `tipo` (que puede remapearse a `sf*` en
   el semestre de formación) y firmar las URLs de los demás endpoints.
2. El usuario elige el periodo (todo el año / trimestre / otro año) y pulsa **buscar** (`fnjs_ver`),
   que llama a `lista_actividades_ctr_data` y muestra la tabla de actividades con sus centros
   encargados.
3. Por actividad, si tiene permiso, puede: pulsar **nuevo** para ver los centros candidatos
   (`fnjs_nuevo_ctr` → `centros_disponibles_data`) y asignar uno (`fnjs_asignar_ctr`), o pulsar un
   centro ya asignado para abrir el popup de orden y elegir **+ prioridad** / **- prioridad**
   (`fnjs_reordenar`) o **borrar** (`fnjs_eliminar`).
4. Tras cada mutación se refresca la celda de centros de esa actividad (`fnjs_actualizar_activ` →
   `centros_encargados_data`).

Los enlaces de acción solo aparecen según los flags de permiso que devuelve el backend
(`perm_crear_ctr`, `perm_modificar_ctr` / `permite_modificar`).

## Ruta de menú

La misma pantalla se abre desde varias entradas de menú según el `tipo` (colectivo):

- **Legacy:** dre > actividades > asignar centros — con variantes activ sg (`tipo=sg`), activ sr
  (`tipo=sr`), sv n y agd (`tipo=nagd`), sf s y sg (`tipo=sfsg`), sf sr (`tipo=sfsr`), sf n, nax y agd
  (`tipo=sfnagd`), sss+ (`tipo=sssc`); también Calendario > actividades > asignar centros y
  vsg/vsr > listas actividades > actividades-centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
