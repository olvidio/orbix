---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Ctr Ficha"
pantalla: "encargossacd.pantalla.ctr_ficha"
preguntas: ["Que se puede hacer en Ctr Ficha?", "Que campos tiene Ctr Ficha?", "Que acciones hay en Ctr Ficha?"]
capacidades: ["encargossacd.ctr_ficha.gestionar", "encargossacd.ctr_get_select.gestionar"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_get_select_data"]
source: "docs/catalogo/encargossacd/pantallas/ctr_ficha.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ctr Ficha

## Resumen

Ficha de atencion sacerdotal de un centro.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `encargossacd.ctr_ficha.gestionar`
- `encargossacd.ctr_get_select.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/ctr_ficha_data`
- `/src/encargossacd/ctr_get_select_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
