---
id: "encargossacd.pantalla.propuestas_menu"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Propuestas Menu"
controller: "frontend/encargossacd/controller/propuestas_menu.php"
vistas: ["frontend/encargossacd/view/propuestas_menu.html.twig"]
fragmentos_frontend: ["frontend/encargossacd/controller/propuestas_lista.php", "frontend/encargossacd/controller/propuestas_aprobar.php", "frontend/encargossacd/controller/propuestas_ajax.php", "frontend/encargossacd/controller/propuestas_lista_sacd.php", "frontend/encargossacd/controller/propuestas_lista_enc.php"]
endpoints: ["/src/encargossacd/propuestas_ajax", "/src/encargossacd/propuestas_aprobar"]
capacidades: []
campos: []
acciones: ["fnjs_new_tabla", "fnjs_aprobar_propuestas", "fnjs_update_div"]
estado_revision: "revisado"
---

# Propuestas Menu

Hub de acciones para propuestas de encargos SACD del nuevo curso: crear/recrear tabla staging,
aprobar (pasar a real), modificar propuestas y listados por SACD / por encargos.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/encargossacd/controller/propuestas_menu.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/propuestas_menu.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/propuestas_lista.php`
- `frontend/encargossacd/controller/propuestas_aprobar.php`
- `frontend/encargossacd/controller/propuestas_ajax.php` (`que=crear_tabla`)
- `frontend/encargossacd/controller/propuestas_lista_sacd.php`
- `frontend/encargossacd/controller/propuestas_lista_enc.php`

## Endpoints Usados

- `/src/encargossacd/propuestas_ajax` (vía FE `crear_tabla`)
- `/src/encargossacd/propuestas_aprobar` (vía FE `propuestas_aprobar.php`)

## Capacidades Relacionadas

Ninguna ficha de capacidad aún (dominio documentado a posteriori).

## Campos Detectados

Ninguno (solo enlaces y confirms).

## Acciones Detectadas

- `fnjs_new_tabla` — confirm + POST `crear_tabla`
- `fnjs_aprobar_propuestas` — confirm + carga `propuestas_aprobar.php`
- `fnjs_update_div` — navega a lista / listados

## Ruta de menú

- **Legacy:** dre > Encargos > propuestas
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > propuestas

Nota: `menus.csv` apunta aún a `apps/encargossacd/controller/propuestas_menu.php`; el runtime
actual es `frontend/encargossacd/controller/propuestas_menu.php`.
