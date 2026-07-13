---
id: "encargossacd.pantalla.horario_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Horario Ver"
controller: "frontend/encargossacd/controller/horario_ver.php"
vistas: ["frontend/encargossacd/view/horario_ver.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/encargo_ver.php", "frontend/encargossacd/controller/horario_update.php"]
endpoints: ["/src/encargossacd/horario_ver_data"]
capacidades: ["encargossacd.horario_ver.gestionar"]
campos: ["html.f_fin", "html.f_ini", "html.h_fin", "html.h_ini", "html.n_sacd", "post.desc_enc", "post.id_enc", "post.id_item_h", "post.mod", "post.origen", "post.refresh"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Horario Ver

Formulario horario de encargo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/horario_ver.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/horario_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/encargo_ver.php`
- `frontend/encargossacd/controller/horario_update.php`

## Endpoints Usados

- `/src/encargossacd/horario_ver_data`

## Capacidades Relacionadas

- `encargossacd.horario_ver.gestionar`

## Campos Detectados

- `html.f_fin`
- `html.f_ini`
- `html.h_fin`
- `html.h_ini`
- `html.n_sacd`
- `post.desc_enc`
- `post.id_enc`
- `post.id_item_h`
- `post.mod`
- `post.origen`
- `post.refresh`

## Acciones Detectadas

- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

