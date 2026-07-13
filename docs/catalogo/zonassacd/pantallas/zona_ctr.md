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

Pantalla Zonas-ctr: listar centros por zona y reasignarlos. Opción sin zona sf solo con perm_des.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/zonassacd/controller/zona_ctr.php`

## Acciones (revisadas)

| Accion | Funcion JS | Llama a | Parametros |
|--------|-----------|---------|------------|
| Listar centros de una zona | `fnjs_busca_ctrs()` (onchange del desplegable) | `zona_ctr_lista_ajax.php` | `id_zona` (`int` / `'no'` / `'no_sf'`) |
| Asignar centros a zona | `fnjs_guardar(form)` (boton asignar) | `zona_ctr_update_ajax.php` | `id_zona_new` (`int` / `'no'`), `sel[]` |

La opcion `'no_sf'` del desplegable solo aparece con `perm_des`. Los centros sf
se muestran con clase `tono2`.

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

## Ruta de menú

- **Legacy:** dre > zonas > zonas-ctr
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-ctr
