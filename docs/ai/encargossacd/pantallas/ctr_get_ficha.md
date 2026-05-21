---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Ctr Get Ficha"
pantalla: "encargossacd.pantalla.ctr_get_ficha"
preguntas: ["Que se puede hacer en Ctr Get Ficha?", "Que campos tiene Ctr Get Ficha?", "Que acciones hay en Ctr Get Ficha?"]
capacidades: ["encargossacd.ctr_get_ficha.gestionar"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
source: "docs/catalogo/encargossacd/pantallas/ctr_get_ficha.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Ctr Get Ficha

## Resumen

Ficha de atencion sacerdotal de un centro.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_ubi`
- `form.seleccion_sacd`
- `html.ok`
- `post.id_ubi`
- `post.seleccion_sacd`

## Acciones Detectadas

- `fnjs_cambiar_lista_sacd`
- `fnjs_cerrar`
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `encargossacd.ctr_get_ficha.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/ctr_get_ficha_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
