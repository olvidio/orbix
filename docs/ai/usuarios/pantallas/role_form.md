---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Role Form"
pantalla: "usuarios.pantalla.role_form"
preguntas: ["Que se puede hacer en Role Form?", "Que campos tiene Role Form?", "Que acciones hay en Role Form?"]
capacidades: ["usuarios.role.gestionar", "usuarios.role_grupmenu_del.gestionar", "usuarios.role_info.gestionar"]
endpoints: ["/src/usuarios/role_grupmenu_del", "/src/usuarios/role_guardar", "/src/usuarios/role_info"]
source: "docs/catalogo/usuarios/pantallas/role_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Role Form

## Resumen

Alta/edición de rol (sf/sv/pau/dmz) y tabla grupmenus asignados.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.dmz`
- `form.pau`
- `form.que`
- `form.role`
- `form.sel`
- `form.sf`
- `form.sv`
- `html.dmz`
- `html.que`
- `html.role`
- `html.sf`
- `html.sv`
- `post.id_role`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_add_grupmenu`
- `fnjs_del_grupmenu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_update_div`

## Capacidades Relacionadas

- `usuarios.role.gestionar`
- `usuarios.role_grupmenu_del.gestionar`
- `usuarios.role_info.gestionar`

## Endpoints Relacionados

- `/src/usuarios/role_grupmenu_del`
- `/src/usuarios/role_guardar`
- `/src/usuarios/role_info`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
