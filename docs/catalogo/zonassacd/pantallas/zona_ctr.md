---
id: "zonassacd.pantalla.zona_ctr"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "zonassacd"
nombre: "Zona Ctr"
controller: "frontend/zonassacd/controller/zona_ctr.php"
vistas: ["frontend/zonassacd/view/zona_ctr.phtml"]
fragmentos_frontend: ["frontend/zonassacd/controller/zona_ctr_lista_ajax.php", "frontend/zonassacd/controller/zona_ctr_update_ajax.php"]
endpoints: ["/src/zonassacd/zona_ctr"]
capacidades: ["zonassacd.zona_ctr.gestionar"]
campos: ["form.id_zona", "form.id_zona_new", "html.id_zona_new", "html.ok"]
acciones: ["fnjs_busca_ctrs", "fnjs_guardar", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Zona Ctr

Pantalla **Zonas-ctr**: consultar que centros pertenecen a cada zona y, con permiso
`des`/`vcsd`, reasignar los centros marcados a otra zona (o dejarlos sin zona).

## Tipo

- Subtipo: `pantalla_principal` (se carga en `#main`; la tabla llega por AJAX)
- Controller: `frontend/zonassacd/controller/zona_ctr.php`

## Vistas Relacionadas

- `frontend/zonassacd/view/zona_ctr.phtml`

## Fragmentos Frontend Relacionados

- `frontend/zonassacd/controller/zona_ctr_lista_ajax.php`
- `frontend/zonassacd/controller/zona_ctr_update_ajax.php`

## Endpoints Usados

- `/src/zonassacd/zona_ctr`

## Capacidades Relacionadas

- `zonassacd.zona_ctr.gestionar`

## Campos Detectados

- `form.id_zona`
- `form.id_zona_new`
- `html.id_zona_new`
- `html.ok`

## Acciones Detectadas

- `fnjs_busca_ctrs`
- `fnjs_guardar`
- `fnjs_left_side_hide`

## Acciones (revisadas)

| Accion | Funcion JS | Llama a | Parametros |
|--------|-----------|---------|------------|
| Listar centros de una zona | `fnjs_busca_ctrs()` (onchange del desplegable) | `zona_ctr_lista_ajax.php` | `id_zona` (`int` / `'no'` / `'no_sf'`) |
| Asignar centros a zona | `fnjs_guardar(form)` (boton asignar) | `zona_ctr_update_ajax.php` | `id_zona_new` (`int` / `'no'`), `sel[]` |

La opcion `'no_sf'` del desplegable solo aparece con `perm_des`. Los centros sf
se muestran con clase `tono2`.

## Manual De Usuario

Ver [`manual/zonassacd.md`](../../../manual/zonassacd.md), seccion *Zona Centros*.

## Revision Manual

- Revisado jun 2026: pantalla principal confirmada; acciones documentadas.
