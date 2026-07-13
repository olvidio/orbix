---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Check First Login 2fa"
flujo: "usuarios.check_first_login_2fa.gestionar.flujo"
preguntas: ["Como ejecutar en Check First Login 2fa?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/check_first_login_2fa"]
source: "docs/catalogo/usuarios/flujos/check_first_login_2fa.md"
estado_revision: "generado"
---

# Ayuda IA - Check First Login 2fa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Check First Login 2fa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Check First Login 2fa?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Tras login web, redirige a configuración 2FA si el usuario no la tiene activada; si no, continúa al home.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
