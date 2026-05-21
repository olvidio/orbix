---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Encargo Ver"
pantalla: "encargossacd.pantalla.encargo_ver"
preguntas: ["Que se puede hacer en Encargo Ver?", "Que campos tiene Encargo Ver?", "Que acciones hay en Encargo Ver?"]
capacidades: ["encargossacd.ctr_get_select.gestionar", "encargossacd.encargo_lst_tipo_enc.gestionar", "encargossacd.encargo_ver.gestionar", "encargossacd.encargo_ver_editar.gestionar", "encargossacd.zonas_get_select.gestionar"]
endpoints: ["/src/encargossacd/ctr_get_select_data", "/src/encargossacd/encargo_lst_tipo_enc_data", "/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_editar", "/src/encargossacd/encargo_ver_nuevo", "/src/encargossacd/zonas_get_select_data"]
source: "docs/catalogo/encargossacd/pantallas/encargo_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Encargo Ver

## Resumen

Ficha de alta/edicion de un encargo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `encargossacd.ctr_get_select.gestionar`
- `encargossacd.encargo_lst_tipo_enc.gestionar`
- `encargossacd.encargo_ver.gestionar`
- `encargossacd.encargo_ver_editar.gestionar`
- `encargossacd.zonas_get_select.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/ctr_get_select_data`
- `/src/encargossacd/encargo_lst_tipo_enc_data`
- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_editar`
- `/src/encargossacd/encargo_ver_nuevo`
- `/src/encargossacd/zonas_get_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
