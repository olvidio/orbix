---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "cambios"
titulo: "Avisos del usuario"
pantalla: "cambios.pantalla.usuario_form_avisos"
preguntas: ["Que se puede hacer en Avisos del usuario?", "Que campos tiene Avisos del usuario?", "Que acciones hay en Avisos del usuario?"]
capacidades: ["cambios.usuario_form_avisos.gestionar"]
endpoints: ["/src/cambios/usuario_form_avisos_data", "/src/cambios/cambio_usuario_objeto_pref_eliminar"]
source: "docs/catalogo/cambios/pantallas/usuario_form_avisos.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Avisos del usuario

## Resumen

Fragmento embebido en la ficha de usuario: tabla de preferencias de aviso (`CambioUsuarioObjetoPref`) configuradas para ese usuario.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `post.id_usuario`
- `post.quien`

## Acciones Detectadas

- `fnjs_add_cambio`
- `fnjs_del_cambio`
- `fnjs_enviar_formulario`
- `fnjs_mod_cambio`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `cambios.usuario_form_avisos.gestionar`

## Endpoints Relacionados

- `/src/cambios/usuario_form_avisos_data`
- `/src/cambios/cambio_usuario_objeto_pref_eliminar`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
