---
id: "encargossacd.pantalla.horario_sacd_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Horario Sacd Ver"
controller: "frontend/encargossacd/controller/horario_sacd_ver.php"
vistas: ["frontend/encargossacd/view/horario_sacd_ver.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/horario_sacd_ex_ver.php", "frontend/encargossacd/controller/horario_sacd_update.php"]
endpoints: ["/src/encargossacd/horario_sacd_ver_data"]
capacidades: ["encargossacd.horario_sacd_ver.gestionar"]
campos: ["html.desc_enc", "html.dia", "html.dia_inc", "html.dia_num", "html.dia_ref", "html.f_fin", "html.f_ini", "html.filtro_sacd", "html.h_fin", "html.h_ini", "html.id_enc", "html.id_item", "html.id_nom", "html.mas_menos", "html.mod", "post.desc_enc", "post.filtro_sacd", "post.id_enc", "post.id_item", "post.id_nom", "post.mod"]
acciones: ["fnjs_enviar_formulario", "fnjs_guardar_horario"]
estado_revision: "revisado"
---

# Horario Sacd Ver

Horario encargo sacd en ficha.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/horario_sacd_ver.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/horario_sacd_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/horario_sacd_ex_ver.php`
- `frontend/encargossacd/controller/horario_sacd_update.php`

## Endpoints Usados

- `/src/encargossacd/horario_sacd_ver_data`

## Capacidades Relacionadas

- `encargossacd.horario_sacd_ver.gestionar`

## Campos Detectados

- `html.desc_enc`
- `html.dia`
- `html.dia_inc`
- `html.dia_num`
- `html.dia_ref`
- `html.f_fin`
- `html.f_ini`
- `html.filtro_sacd`
- `html.h_fin`
- `html.h_ini`
- `html.id_enc`
- `html.id_item`
- `html.id_nom`
- `html.mas_menos`
- `html.mod`
- `post.desc_enc`
- `post.filtro_sacd`
- `post.id_enc`
- `post.id_item`
- `post.id_nom`
- `post.mod`

## Acciones Detectadas

- `fnjs_enviar_formulario`
- `fnjs_guardar_horario`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

