---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "ubis"
titulo: "List Ctr"
pantalla: "ubis.pantalla.list_ctr"
preguntas: ["Que se puede hacer en List Ctr?", "Que campos tiene List Ctr?", "Que acciones hay en List Ctr?"]
capacidades: ["ubis.list_ctr.gestionar"]
endpoints: ["/src/ubis/list_ctr_data"]
source: "docs/catalogo/ubis/pantallas/list_ctr.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - List Ctr

## Resumen

Pantalla principal de listado de centros y casas con filtros por delegación y tipo.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.loc`
- `form.que_lista`
- `form.sel`
- `post.loc`
- `post.que_lista`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_cerrar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_limpiar`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`
- `fnjs_ver_dl`

## Capacidades Relacionadas

- `ubis.list_ctr.gestionar`

## Endpoints Relacionados

- `/src/ubis/list_ctr_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
