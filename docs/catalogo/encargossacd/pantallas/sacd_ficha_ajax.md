---
id: "encargossacd.pantalla.sacd_ficha_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Sacd Ficha Ajax"
controller: "frontend/encargossacd/controller/sacd_ficha_ajax.php"
vistas: ["frontend/encargossacd/view/sacd_ficha_ajax_ficha.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/ctr_ficha.php", "frontend/encargossacd/controller/horario_ver.php", "frontend/encargossacd/controller/sacd_ficha_ajax.php"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update", "/src/encargossacd/sacd_select_data"]
capacidades: ["encargossacd.sacd_ficha.gestionar", "encargossacd.sacd_select.gestionar"]
campos: ["form.dedic_m", "form.dedic_t", "form.dedic_v", "form.enc_num", "form.id_tipo_enc", "form.mas", "form.observ", "html.dedic_m[<?= $j ?>]", "html.dedic_t[<?= $j ?>]", "html.dedic_v[<?= $j ?>]", "html.enc_num", "html.ok", "post.filtro_sacd", "post.id_nom", "post.que"]
acciones: ["fnjs_crear_horario", "fnjs_guardar", "fnjs_mas_enc", "fnjs_update_div", "fnjs_ver_ficha"]
estado_revision: "revisado"
---

# Sacd Ficha Ajax

AJAX de la ficha SACD.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/sacd_ficha_ajax_ficha.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/ctr_ficha.php`
- `frontend/encargossacd/controller/horario_ver.php`
- `frontend/encargossacd/controller/sacd_ficha_ajax.php`

## Endpoints Usados

- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`
- `/src/encargossacd/sacd_select_data`

## Capacidades Relacionadas

- `encargossacd.sacd_ficha.gestionar`
- `encargossacd.sacd_select.gestionar`

## Campos Detectados

- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `form.enc_num`
- `form.id_tipo_enc`
- `form.mas`
- `form.observ`
- `html.dedic_m[<?= $j ?>]`
- `html.dedic_t[<?= $j ?>]`
- `html.dedic_v[<?= $j ?>]`
- `html.enc_num`
- `html.ok`
- `post.filtro_sacd`
- `post.id_nom`
- `post.que`

## Acciones Detectadas

- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_mas_enc`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

