---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Grupo Lista"
pantalla: "usuarios.pantalla.grupo_lista"
preguntas: ["Que se puede hacer en Grupo Lista?", "Que campos tiene Grupo Lista?", "Que acciones hay en Grupo Lista?"]
capacidades: ["usuarios.grupo.gestionar"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_lista"]
source: "docs/catalogo/usuarios/pantallas/grupo_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Grupo Lista

## Resumen

Descripcion funcional pendiente de revisar.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `form.username`
- `html.btn_ok`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`
- `post.username`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Capacidades Relacionadas

- `usuarios.grupo.gestionar`

## Endpoints Relacionados

- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_lista`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
