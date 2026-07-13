---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Usuario Lista"
pantalla: "usuarios.pantalla.usuario_lista"
preguntas: ["Que se puede hacer en Usuario Lista?", "Que campos tiene Usuario Lista?", "Que acciones hay en Usuario Lista?"]
capacidades: ["usuarios.usuario.gestionar"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_lista"]
source: "docs/catalogo/usuarios/pantallas/usuario_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Lista

## Resumen

Listado principal de usuarios web con filtro por login, alta/edición y borrado.

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
- `fnjs_solo_uno`
- `fnjs_update_div`

## Capacidades Relacionadas

- `usuarios.usuario.gestionar`

## Endpoints Relacionados

- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_lista`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
