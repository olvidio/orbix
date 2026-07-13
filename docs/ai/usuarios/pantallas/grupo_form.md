---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Grupo Form"
pantalla: "usuarios.pantalla.grupo_form"
preguntas: ["Que se puede hacer en Grupo Form?", "Que campos tiene Grupo Form?", "Que acciones hay en Grupo Form?"]
capacidades: ["usuarios.grupo_info.gestionar", "usuarios.perm_menu.gestionar"]
endpoints: ["/src/usuarios/grupo_info", "/src/usuarios/perm_menu_eliminar", "/src/usuarios/perm_menu_lista"]
source: "docs/catalogo/usuarios/pantallas/grupo_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Grupo Form

## Resumen

Formulario alta/edición de grupo de permisos.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.que`
- `form.sel`
- `form.usuario`
- `html.que`
- `html.refresh`
- `post.id_usuario`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_add_perm_menu`
- `fnjs_del_perm_menu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `usuarios.grupo_info.gestionar`
- `usuarios.perm_menu.gestionar`

## Endpoints Relacionados

- `/src/usuarios/grupo_info`
- `/src/usuarios/perm_menu_eliminar`
- `/src/usuarios/perm_menu_lista`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
