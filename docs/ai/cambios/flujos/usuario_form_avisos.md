---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "cambios"
titulo: "los avisos de cambios de un usuario"
flujo: "cambios.usuario_form_avisos.gestionar.flujo"
preguntas: ["Como gestionar los avisos de cambios de un usuario en los avisos de cambios de un usuario?"]
pantallas_principales: ["cambios.pantalla.usuario_form_avisos"]
fragmentos: ["cambios.pantalla.usuario_form_avisos"]
endpoints: ["/src/cambios/usuario_form_avisos_data", "/src/cambios/cambio_usuario_objeto_pref_eliminar"]
source: "docs/catalogo/cambios/flujos/usuario_form_avisos.md"
estado_revision: "generado"
---

# Ayuda IA - los avisos de cambios de un usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `los avisos de cambios de un usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar los avisos de cambios de un usuario en los avisos de cambios de un usuario?

## Donde Entrar

- Avisos del usuario (`cambios.pantalla.usuario_form_avisos`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar los avisos de cambios de un usuario

1. En la ficha del usuario, apartado «avisos de cambios», se ve la tabla de avisos (o «no hay ninguna fila»).
2. Pulsar **añadir aviso** para crear uno, o marcar una fila y modificarla o eliminarla.

Referencias tecnicas para verificar la respuesta:
- `/src/cambios/usuario_form_avisos_data`
- `/src/cambios/cambio_usuario_objeto_pref_eliminar`

## Pantallas Y Fragmentos Relacionados

- `cambios.pantalla.usuario_form_avisos`
- `cambios.pantalla.usuario_form_avisos`

## Objetivo

Ver y gestionar los avisos de cambios en actividades que recibe un usuario.

## Errores Documentados

- `No tiene permiso`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
