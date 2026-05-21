---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Sacd Ausencias Get"
pantalla: "encargossacd.pantalla.sacd_ausencias_get"
preguntas: ["Que se puede hacer en Sacd Ausencias Get?", "Que campos tiene Sacd Ausencias Get?", "Que acciones hay en Sacd Ausencias Get?"]
capacidades: ["encargossacd.sacd_ausencias_get.gestionar"]
endpoints: ["/src/encargossacd/sacd_ausencias_get_data"]
source: "docs/catalogo/encargossacd/pantallas/sacd_ausencias_get.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Sacd Ausencias Get

## Resumen

Muestra las ausencias de un SACD.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.fin`
- `form.id_enc`
- `form.id_item`
- `form.inicio`
- `html.ok`
- `post.filtro_sacd`
- `post.historial`
- `post.id_nom`

## Acciones Detectadas

- `fnjs_date_fin`
- `fnjs_guardar`
- `fnjs_horario`
- `fnjs_mas_enc`
- `fnjs_update_div`

## Capacidades Relacionadas

- `encargossacd.sacd_ausencias_get.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/sacd_ausencias_get_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
