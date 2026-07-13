---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Perm Activ Lista"
pantalla: "usuarios.pantalla.perm_activ_lista"
preguntas: ["Que se puede hacer en Perm Activ Lista?", "Que campos tiene Perm Activ Lista?", "Que acciones hay en Perm Activ Lista?"]
capacidades: ["usuarios.perm_activ.gestionar"]
endpoints: ["/src/usuarios/perm_activ_lista"]
source: "docs/catalogo/usuarios/pantallas/perm_activ_lista.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Perm Activ Lista

## Resumen

Pestaña permisos actividad-proceso en ficha usuario.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.que`
- `form.sel`
- `html.que`
- `post.id_usuario`
- `post.olvidar`
- `post.quien`

## Acciones Detectadas

- `fnjs_add_perm_activ`
- `fnjs_del_perm_activ`
- `fnjs_enviar_formulario`
- `fnjs_mod_perm_activ`
- `fnjs_solo_uno`

## Capacidades Relacionadas

- `usuarios.perm_activ.gestionar`

## Endpoints Relacionados

- `/src/usuarios/perm_activ_lista`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
