---
id: "actividadescentro.activ_ctr_shell.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Abrir la asignación de centros encargados"
capacidad: "actividadescentro.activ_ctr_shell.gestionar"
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/activ_ctr_shell_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Activ Ctr Shell

Bootstrap de la pantalla `activ_ctr`: resuelve el `tipo` efectivo y prepara las URLs de los demás
endpoints del módulo.

## Objetivo De Usuario

Preparar la pantalla de asignación de centros encargados antes de mostrar los filtros de periodo.

## Punto De Entrada

Menú de asignación de centros encargados a las actividades.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Abrir la asignación de centros encargados

Pasos propuestos:
1. Entrar en la pantalla de centros encargados.
2. La pantalla se prepara sola y muestra los filtros de periodo.

Endpoints asociados:
- `/src/actividadescentro/activ_ctr_shell_data`

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
