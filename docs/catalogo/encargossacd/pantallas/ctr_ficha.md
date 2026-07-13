---
id: "encargossacd.pantalla.ctr_ficha"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "encargossacd"
nombre: "Ctr Ficha"
controller: "frontend/encargossacd/controller/ctr_ficha.php"
vistas: ["frontend/encargossacd/view/ctr_ficha.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/ctr_ficha_update.php", "frontend/encargossacd/controller/ctr_get_ficha.php"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_get_select_data"]
capacidades: ["encargossacd.ctr_ficha.gestionar", "encargossacd.ctr_get_select.gestionar"]
campos: ["form.filtro_ctr", "form.id_ubi", "post.filtro_ctr", "post.id_ubi"]
acciones: ["fnjs_construir_desplegable", "fnjs_guardar", "fnjs_lista_ctrs", "fnjs_ver_ficha"]
estado_revision: "revisado"
---

# Ctr Ficha

Ficha de atencion sacerdotal de un centro.

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/encargossacd/controller/ctr_ficha.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/ctr_ficha.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/ctr_ficha_update.php`
- `frontend/encargossacd/controller/ctr_get_ficha.php`

## Endpoints Usados

- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_get_select_data`

## Capacidades Relacionadas

- `encargossacd.ctr_ficha.gestionar`
- `encargossacd.ctr_get_select.gestionar`

## Campos Detectados

- `form.filtro_ctr`
- `form.id_ubi`
- `post.filtro_ctr`
- `post.id_ubi`

## Acciones Detectadas

- `fnjs_construir_desplegable`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_ver_ficha`

## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr

## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr


## Ruta de menú

- **Legacy:** dre > Encargos > ficha ctr
- **Pills2:** ATENCIÓN SACD > Encargos sacd (ctr, etc.) > Ficha ctr

