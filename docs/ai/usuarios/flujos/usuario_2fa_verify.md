---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Usuario 2fa Verify"
flujo: "usuarios.usuario_2fa_verify.gestionar.flujo"
preguntas: ["Como ejecutar en Usuario 2fa Verify?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
endpoints: ["/src/usuarios/usuario_2fa_verify"]
source: "docs/catalogo/usuarios/flujos/usuario_2fa_verify.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario 2fa Verify

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario 2fa Verify`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Usuario 2fa Verify?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_2fa`

## Objetivo

Valida código TOTP contra secret provisional (paso previo a activar 2FA).

## Errores Documentados

- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
