---
id: "encargossacd.pantalla.encargo_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "encargossacd"
nombre: "Encargo Ver"
controller: "frontend/encargossacd/controller/encargo_ver.php"
vistas: ["frontend/encargossacd/view/encargo_ver.phtml"]
fragmentos_frontend: ["frontend/encargossacd/controller/encargo_ver.php"]
endpoints: ["/src/encargossacd/ctr_get_select_data", "/src/encargossacd/encargo_lst_tipo_enc_data", "/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_editar", "/src/encargossacd/encargo_ver_nuevo", "/src/encargossacd/zonas_get_select_data"]
capacidades: ["encargossacd.ctr_get_select.gestionar", "encargossacd.encargo_lst_tipo_enc.gestionar", "encargossacd.encargo_ver.gestionar", "encargossacd.encargo_ver_editar.gestionar", "encargossacd.zonas_get_select.gestionar"]
campos: ["form.desc_enc", "form.desc_lugar", "form.filtro_ctr", "form.grupo", "form.id_enc", "form.id_tipo_enc", "form.id_zona", "form.idioma_enc", "form.lst_ctrs", "form.nom_tipo", "form.observ", "form.que", "html.desc_enc", "html.desc_lugar", "post.desc_enc", "post.desc_lugar", "post.filtro_ctr", "post.grupo", "post.id_enc", "post.id_tipo_enc", "post.id_zona", "post.que", "post.refresh", "post.sel"]
acciones: ["fnjs_construir_desplegable", "fnjs_generarNomEnc", "fnjs_guardar", "fnjs_lista_ctrs", "fnjs_lista_ctrs_por_zona", "fnjs_lista_zonas", "fnjs_lst_tipo_enc", "fnjs_strip_hash_params"]
estado_revision: "revisado"
---

# Encargo Ver

Ficha de alta/edicion de un encargo.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/encargossacd/controller/encargo_ver.php`

## Vistas Relacionadas

- `frontend/encargossacd/view/encargo_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/encargossacd/controller/encargo_ver.php`

## Endpoints Usados

- `/src/encargossacd/ctr_get_select_data`
- `/src/encargossacd/encargo_lst_tipo_enc_data`
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_editar`
- `/src/encargossacd/encargo_ver_nuevo`
- `/src/encargossacd/zonas_get_select_data`

## Capacidades Relacionadas

- `encargossacd.ctr_get_select.gestionar`
- `encargossacd.encargo_lst_tipo_enc.gestionar`
- `encargossacd.encargo_ver.gestionar`
- `encargossacd.encargo_ver_editar.gestionar`
- `encargossacd.zonas_get_select.gestionar`

## Campos Detectados

- `form.desc_enc`
- `form.desc_lugar`
- `form.filtro_ctr`
- `form.grupo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_zona`
- `form.idioma_enc`
- `form.lst_ctrs`
- `form.nom_tipo`
- `form.observ`
- `form.que`
- `html.desc_enc`
- `html.desc_lugar`
- `post.desc_enc`
- `post.desc_lugar`
- `post.filtro_ctr`
- `post.grupo`
- `post.id_enc`
- `post.id_tipo_enc`
- `post.id_zona`
- `post.que`
- `post.refresh`
- `post.sel`

## Acciones Detectadas

- `fnjs_construir_desplegable`
- `fnjs_generarNomEnc`
- `fnjs_guardar`
- `fnjs_lista_ctrs`
- `fnjs_lista_ctrs_por_zona`
- `fnjs_lista_zonas`
- `fnjs_lst_tipo_enc`
- `fnjs_strip_hash_params`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

