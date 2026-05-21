---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Usuario Perm Activ Ajax"
flujo: "procesos.usuario_perm_activ_ajax.gestionar.flujo"
preguntas: ["Como ejecutar en Usuario Perm Activ Ajax?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
endpoints: ["/src/procesos/usuario_perm_activ_ajax"]
source: "docs/catalogo/procesos/flujos/usuario_perm_activ_ajax.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Perm Activ Ajax

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Perm Activ Ajax`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Usuario Perm Activ Ajax?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.usuario_perm_activ`

## Objetivo

Gestiona UsuarioPermActivFases. Caso de uso: devuelve las opciones disponibles para el desplegable fase_ref[] de la pantalla usuario_perm_activ, filtradas por el tipo de actividad y la delegacion.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
