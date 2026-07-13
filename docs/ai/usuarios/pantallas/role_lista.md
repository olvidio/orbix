---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Role Lista"
pantalla: "usuarios.pantalla.role_lista"
preguntas: ["Que se puede hacer en Role Lista?", "Que campos tiene Role Lista?", "Que acciones hay en Role Lista?"]
capacidades: ["usuarios.role.gestionar"]
endpoints: ["/src/usuarios/role_eliminar", "/src/usuarios/role_lista"]
source: "docs/catalogo/usuarios/pantallas/role_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Role Lista

## Resumen

Listado de roles con grupmenus asociados; CRUD según permiso superadmin/admin.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.sel`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Capacidades Relacionadas

- `usuarios.role.gestionar`

## Endpoints Relacionados

- `/src/usuarios/role_eliminar`
- `/src/usuarios/role_lista`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
