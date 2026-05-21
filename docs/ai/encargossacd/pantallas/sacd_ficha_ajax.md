---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Sacd Ficha Ajax"
pantalla: "encargossacd.pantalla.sacd_ficha_ajax"
preguntas: ["Que se puede hacer en Sacd Ficha Ajax?", "Que campos tiene Sacd Ficha Ajax?", "Que acciones hay en Sacd Ficha Ajax?"]
capacidades: ["encargossacd.sacd_ficha.gestionar", "encargossacd.sacd_select.gestionar"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update", "/src/encargossacd/sacd_select_data"]
source: "docs/catalogo/encargossacd/pantallas/sacd_ficha_ajax.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Sacd Ficha Ajax

## Resumen

AJAX de la ficha SACD.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `encargossacd.sacd_ficha.gestionar`
- `encargossacd.sacd_select.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`
- `/src/encargossacd/sacd_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
