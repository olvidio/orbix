---
id: "actividadescentro.activ_ctr_shell.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Activ Ctr Shell"
capacidad: "actividadescentro.activ_ctr_shell.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/activ_ctr_shell_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Activ Ctr Shell

Bootstrap de la pantalla `activ_ctr`: resuelve el `tipo` efectivo y prepara las URLs de los demás
endpoints del módulo.

## Objetivo De Usuario

Al abrir la pantalla de asignación de centros encargados, el sistema resuelve el colectivo (`tipo`,
que puede remaparse a `sf*` en el semestre de formación) y firma las URLs AJAX que usará el resto de
acciones. Es un paso transparente para el usuario, previo a mostrar los filtros de periodo.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): al cargarse invoca
`activ_ctr_shell_data` vía `PostRequest::getDataFromUrl`.

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
- `form.empiezamax`
- `form.empiezamin`
- `form.periodo`
- `form.tipo`
- `form.year`
- `post.periodo`
- `post.tipo`
- `post.year`

Acciones JavaScript:
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

## Endpoints Del Flujo

- `/src/actividadescentro/activ_ctr_shell_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
