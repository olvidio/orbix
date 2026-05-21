---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Encargo Select"
pantalla: "encargossacd.pantalla.encargo_select"
preguntas: ["Que se puede hacer en Encargo Select?", "Que campos tiene Encargo Select?", "Que acciones hay en Encargo Select?"]
capacidades: ["encargossacd.encargo_select.gestionar", "encargossacd.encargo_ver.gestionar"]
endpoints: ["/src/encargossacd/encargo_select_data", "/src/encargossacd/encargo_ver_eliminar"]
source: "docs/catalogo/encargossacd/pantallas/encargo_select.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Encargo Select

## Resumen

Listado de encargos.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_activ`
- `form.id_nom`
- `form.que`
- `form.scroll_id`
- `form.sel`
- `html.desc_enc`
- `html.ok`
- `html.que`
- `post.desc_enc`
- `post.id_tipo_enc`
- `post.stack`
- `post.titulo`

## Acciones Detectadas

- `fnjs_borrar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_horario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_strip_hash_sel`
- `fnjs_update_div`

## Capacidades Relacionadas

- `encargossacd.encargo_select.gestionar`
- `encargossacd.encargo_ver.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/encargo_select_data`
- `/src/encargossacd/encargo_ver_eliminar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
