---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "Usuario Avisos Pref"
flujo: "cambios.usuario_avisos_pref.gestionar.flujo"
preguntas: ["Como abrir el formulario en Usuario Avisos Pref?"]
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref"]
endpoints: ["/src/cambios/usuario_avisos_pref_form_data"]
source: "docs/catalogo/cambios/flujos/usuario_avisos_pref.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Avisos Pref

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Avisos Pref`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el formulario en Usuario Avisos Pref?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/usuario_avisos_pref_form_data`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_avisos_pref`

## Objetivo

Gestiona UsuarioAvisosPref. Endpoint JSON que devuelve la informacion necesaria para pintar el formulario usuario_avisos_pref (edicion de un aviso de usuario/grupo).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
