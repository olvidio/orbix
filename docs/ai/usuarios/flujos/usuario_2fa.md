---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Activar la verificación en dos pasos"
flujo: "usuarios.usuario_2fa.gestionar.flujo"
preguntas: ["Como activar la verificación en dos pasos en Activar la verificación en dos pasos?"]
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_reset_2fa"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
source: "docs/catalogo/usuarios/flujos/usuario_2fa.md"
estado_revision: "generado"
---

# Ayuda IA - Activar la verificación en dos pasos

Usa este documento para responder preguntas de usuario sobre como trabajar con `Activar la verificación en dos pasos`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como activar la verificación en dos pasos en Activar la verificación en dos pasos?

## Donde Entrar

- Usuario Form 2fa (`usuarios.pantalla.usuario_form_2fa`)
- Usuario Reset 2fa (`usuarios.pantalla.usuario_reset_2fa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Activar la verificación en dos pasos

1. Descargar una aplicación de autenticación (Google Authenticator, Microsoft Authenticator o Authy).
2. Escanear el **Código QR** o escribir la **Clave secreta**.
3. Escribir el **Código de verificación** que muestra la aplicación y marcar **Activar autenticación de dos factores**.
4. Guardar. Desde ese momento, en cada acceso se pide contraseña y código.
5. Si falta el código aparece «Se requiere un código de verificación para activar 2FA».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_2fa_update`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_reset_2fa`

## Objetivo

Activar o desactivar la verificación en dos pasos (2FA) de un usuario.

## Errores Documentados

- `Usuario no encontrado`
- `Se requiere un código de verificación para activar 2FA`
- `Código de verificación inválido`
- `Hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
