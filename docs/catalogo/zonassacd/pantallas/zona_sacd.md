---
id: "zonassacd.pantalla.zona_sacd"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "zonassacd"
nombre: "Zona Sacd"
controller: "frontend/zonassacd/controller/zona_sacd.php"
vistas: ["frontend/zonassacd/view/zona_sacd.phtml"]
fragmentos_frontend: ["frontend/zonassacd/controller/zona_sacd_lista_ajax.php", "frontend/zonassacd/controller/zona_sacd_update_ajax.php"]
endpoints: ["/src/misas/zona_sacd_datos_get", "/src/misas/zona_sacd_datos_put", "/src/zonassacd/zona_sacd"]
capacidades: ["zonassacd.zona_sacd.gestionar"]
campos: ["form.dw1", "form.dw2", "form.dw3", "form.dw4", "form.dw5", "form.dw6", "form.dw7", "form.id_sacd", "form.id_zona", "form.id_zona_new", "html.dw1", "html.dw2", "html.dw3", "html.dw4", "html.dw5", "html.dw6", "html.dw7", "html.id_zona", "html.id_zona_new", "html.ok", "html.ok2"]
acciones: ["fnjs_busca_sacds", "fnjs_guardar", "fnjs_left_side_hide", "fnjs_modal_zona_sacd_ver", "fnjs_modificar", "fnjs_solo_uno"]
estado_revision: "revisado"
---

# Zona Sacd

Pantalla Zonas-sacd: listar sacd por zona, reasignar zonas (propia/iglesia) y modal de días L–D (vía misas/zona_sacd_datos_*). Requiere perm_des para mutaciones.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/zonassacd/controller/zona_sacd.php`

## Acciones (revisadas)

| Accion | Funcion JS | Llama a | Parametros |
|--------|-----------|---------|------------|
| Listar sacds de una zona | `fnjs_busca_sacds()` (onchange del desplegable) | `zona_sacd_lista_ajax.php` | `id_zona` (`int` / `'no'`) |
| Cambiar asignacion de zona | `fnjs_guardar(form, 1)` | `zona_sacd_update_ajax.php` | `id_zona`, `id_zona_new`, `acumular=1`, `sel[]` |
| Añadir asignacion iglesia/cgi | `fnjs_guardar(form, 2)` | idem | idem con `acumular=2` |
| Abrir modal de dias (1 sacd marcado) | boton `modificar` de la tabla → `fnjs_modificar(form)` → `fnjs_modal_zona_sacd_ver(id_sacd)` | `/src/misas/zona_sacd_datos_get` | `id_zona`, `id_sacd` |
| Grabar dias de la semana | boton Grabar del modal (`commitCurrentEdit`) | `/src/misas/zona_sacd_datos_put` | `id_zona`, `id_sacd`, `dw1..dw7` |

**Validaciones en cliente**: zona destino obligatoria; al menos un sacd marcado;
el modal exige exactamente un sacd marcado (`fnjs_solo_uno`).

## Vistas Relacionadas

- `frontend/zonassacd/view/zona_sacd.phtml`

## Fragmentos Frontend Relacionados

- `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`
- `frontend/zonassacd/controller/zona_sacd_update_ajax.php`

## Endpoints Usados

- `/src/misas/zona_sacd_datos_get`
- `/src/misas/zona_sacd_datos_put`
- `/src/zonassacd/zona_sacd`

## Capacidades Relacionadas

- `zonassacd.zona_sacd.gestionar`

## Campos Detectados

- `form.dw1`
- `form.dw2`
- `form.dw3`
- `form.dw4`
- `form.dw5`
- `form.dw6`
- `form.dw7`
- `form.id_sacd`
- `form.id_zona`
- `form.id_zona_new`
- `html.dw1`
- `html.dw2`
- `html.dw3`
- `html.dw4`
- `html.dw5`
- `html.dw6`
- `html.dw7`
- `html.id_zona`
- `html.id_zona_new`
- `html.ok`
- `html.ok2`

## Acciones Detectadas

- `fnjs_busca_sacds`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_modal_zona_sacd_ver`
- `fnjs_modificar`
- `fnjs_solo_uno`

## Ruta de menú

- **Legacy:** dre > zonas > zonas-sacd
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-sacd
