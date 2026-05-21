---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "notas"
titulo: "Actividad Buscar Form"
pantalla: "notas.pantalla.actividad_buscar_form"
preguntas: ["Que se puede hacer en Actividad Buscar Form?", "Que campos tiene Actividad Buscar Form?", "Que acciones hay en Actividad Buscar Form?"]
capacidades: ["notas.actividades_buscar.gestionar"]
endpoints: ["/src/notas/actividades_buscar_data"]
source: "docs/catalogo/notas/pantallas/actividad_buscar_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Actividad Buscar Form

## Resumen

Dialogo "buscar actividad" que abre `form_notas_de_una_persona.phtml` al pulsar "añadir ca".

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.observ`
- `form.pres_mail`
- `form.pres_nom`
- `form.pres_telf`
- `form.zona`
- `post.dl_org`
- `post.f_acta_iso`
- `post.id_activ`

## Acciones Detectadas

- `fnjs_buscar_ca`
- `fnjs_cerrar`
- `fnjs_update_activ`

## Capacidades Relacionadas

- `notas.actividades_buscar.gestionar`

## Endpoints Relacionados

- `/src/notas/actividades_buscar_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
