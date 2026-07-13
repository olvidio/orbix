---
id: "cambios.pantalla.usuario_form_avisos"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cambios"
nombre: "Avisos del usuario"
controller: "frontend/cambios/controller/usuario_form_avisos.php"
vistas: ["frontend/cambios/view/usuario_form_avisos.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/usuario_avisos_pref.php"]
endpoints: ["/src/cambios/usuario_form_avisos_data", "/src/cambios/cambio_usuario_objeto_pref_eliminar"]
capacidades: ["cambios.usuario_form_avisos.gestionar"]
campos: ["post.id_usuario", "post.quien"]
acciones: ["fnjs_add_cambio", "fnjs_del_cambio", "fnjs_enviar_formulario", "fnjs_mod_cambio", "fnjs_solo_uno"]
estado_revision: "revisado"
---

# Avisos del usuario

Fragmento embebido en la ficha de usuario: tabla de preferencias de aviso (`CambioUsuarioObjetoPref`)
configuradas para ese usuario.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cambios/controller/usuario_form_avisos.php`

## Vistas Relacionadas

- `frontend/cambios/view/usuario_form_avisos.phtml`

## Endpoints Usados

- `/src/cambios/usuario_form_avisos_data` (tabla inicial)
- `/src/cambios/cambio_usuario_objeto_pref_eliminar` (`fnjs_del_cambio`)

## Acciones Detectadas

- `fnjs_add_cambio` / `fnjs_mod_cambio` — abren `usuario_avisos_pref` (nuevo/modificar)
- `fnjs_del_cambio` — elimina la preferencia seleccionada
- `fnjs_solo_uno` — exige una fila seleccionada

## Manual De Usuario

Desde la ficha de un usuario web, pestaña de avisos: ver las reglas configuradas, añadir una nueva,
modificar o eliminar la seleccionada.

## Ruta de menú

sin entrada de menú en el índice (se abre embebido desde la ficha de usuario)
