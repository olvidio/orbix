---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "encargossacd"
titulo: "Horario Ver"
pantalla: "encargossacd.pantalla.horario_ver"
preguntas: ["Que se puede hacer en Horario Ver?", "Que campos tiene Horario Ver?", "Que acciones hay en Horario Ver?"]
capacidades: ["encargossacd.horario_ver.gestionar"]
endpoints: ["/src/encargossacd/horario_ver_data"]
source: "docs/catalogo/encargossacd/pantallas/horario_ver.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Horario Ver

## Resumen

Formulario horario de encargo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `encargossacd.horario_ver.gestionar`

## Endpoints Relacionados

- `/src/encargossacd/horario_ver_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
