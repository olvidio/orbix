---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Reiniciar el 2FA por correo"
flujo: "usuarios.recuperar_2fa_mail.gestionar.flujo"
preguntas: ["Como reiniciar el 2fa por correo en Reiniciar el 2FA por correo?"]
pantallas_principales: ["usuarios.pantalla.recuperar_2fa"]
fragmentos: []
endpoints: ["/src/usuarios/recuperar_2fa_mail"]
source: "docs/catalogo/usuarios/flujos/recuperar_2fa_mail.md"
estado_revision: "generado"
---

# Ayuda IA - Reiniciar el 2FA por correo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Reiniciar el 2FA por correo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como reiniciar el 2fa por correo en Reiniciar el 2FA por correo?

## Donde Entrar

- Recuperar 2fa (`usuarios.pantalla.recuperar_2fa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Reiniciar el 2fa por correo

1. En la pantalla de acceso, pulsar el enlace de ayuda del 2FA.
2. Indicar el login y el esquema.
3. Se envía un correo con un enlace; al usarlo se desactiva el 2FA y hay que configurarlo de nuevo.
4. Si el usuario no tiene correo registrado aparece «No hay email asociado a este usuario»; hay que pedir al administrador que lo restablezca.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/recuperar_2fa_mail`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.recuperar_2fa`

## Objetivo

Recuperar el acceso cuando se ha perdido la aplicación de autenticación: envía un enlace por correo para reiniciar el 2FA.

## Errores Documentados

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
