---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Verificar el código"
flujo: "usuarios.usuario_2fa_verify.gestionar.flujo"
preguntas: ["Como verificar el código en Verificar el código?"]
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
endpoints: ["/src/usuarios/usuario_2fa_verify"]
source: "docs/catalogo/usuarios/flujos/usuario_2fa_verify.md"
estado_revision: "generado"
---

# Ayuda IA - Verificar el código

Usa este documento para responder preguntas de usuario sobre como trabajar con `Verificar el código`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como verificar el código en Verificar el código?

## Donde Entrar

- Usuario Form 2fa (`usuarios.pantalla.usuario_form_2fa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Verificar el código

1. Escanear el QR con la aplicación.
2. Escribir en **Código de verificación** el número que muestra.
3. Si es correcto se permite activar 2FA; si no, aparece «Código de verificación inválido».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_2fa_verify`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_2fa`

## Objetivo

Comprobar el código de la aplicación de autenticación antes de activar la verificación en dos pasos.

## Errores Documentados

- `Código de verificación o clave secreta no válidos`
- `Código de verificación inválido`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
