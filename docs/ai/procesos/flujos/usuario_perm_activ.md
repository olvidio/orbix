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

Carga de la pantalla de alta o edición de permisos de actividad para un usuario: tipo de actividad, filas de ámbitos afectados y desplegables de fase y permisos.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
