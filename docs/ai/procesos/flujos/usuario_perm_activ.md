---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Usuario Perm Activ"
flujo: "procesos.usuario_perm_activ.gestionar.flujo"
preguntas: ["Como obtener datos en Usuario Perm Activ?"]
pantallas_principales: []
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
endpoints: ["/src/procesos/usuario_perm_activ_data"]
source: "docs/catalogo/procesos/flujos/usuario_perm_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Perm Activ

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Perm Activ`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Usuario Perm Activ?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.usuario_perm_activ`

## Objetivo

Gestiona UsuarioPermActiv. Caso de uso: datos para la pantalla usuario_perm_activ (alta/edicion de permisos de actividad para un usuario). Agrupa la resolucion de repositorios para que el controlador frontend no acceda directamente al contenedor ni a use src\.... El frontend recibe arrays serializables y construye los frontend\shared\web\Desplegable.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
