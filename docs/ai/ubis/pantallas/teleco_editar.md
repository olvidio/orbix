---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "Teleco Editar"
pantalla: "ubis.pantalla.teleco_editar"
preguntas: ["Que se puede hacer en Teleco Editar?", "Que campos tiene Teleco Editar?", "Que acciones hay en Teleco Editar?"]
capacidades: ["ubis.teleco_editar.gestionar"]
endpoints: ["/src/ubis/teleco_editar"]
source: "docs/catalogo/ubis/pantallas/teleco_editar.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Teleco Editar

## Resumen

Formulario modal de alta o edición de una telecomunicación del ubi.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_desc_teleco`
- `form.id_tipo_teleco`
- `form.mod`
- `form.num_teleco`
- `form.observ`
- `html.mod`
- `html.num_teleco`
- `html.observ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.s_pkey`
- `post.sel`

## Acciones Detectadas

- `fnjs_actualizar_descripcion`
- `fnjs_guardar`

## Capacidades Relacionadas

- `ubis.teleco_editar.gestionar`

## Endpoints Relacionados

- `/src/ubis/teleco_editar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
