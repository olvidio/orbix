---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Recuperar la contraseña por correo"
flujo: "usuarios.recuperar_password_mail.gestionar.flujo"
preguntas: ["Como recuperar la contraseña por correo en Recuperar la contraseña por correo?"]
pantallas_principales: ["usuarios.pantalla.recuperar_password"]
fragmentos: []
endpoints: ["/src/usuarios/recuperar_password_mail"]
source: "docs/catalogo/usuarios/flujos/recuperar_password_mail.md"
estado_revision: "generado"
---

# Ayuda IA - Recuperar la contraseña por correo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Recuperar la contraseña por correo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como recuperar la contraseña por correo en Recuperar la contraseña por correo?

## Donde Entrar

- Recuperar Password (`usuarios.pantalla.recuperar_password`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Recuperar la contraseña por correo

1. En la pantalla de acceso, pulsar el enlace de ayuda para recuperar la contraseña.
2. Indicar el login.
3. El sistema envía al correo del usuario una contraseña temporal y marca que debe cambiarla al entrar.
4. Con la temporal, iniciar sesión y elegir una contraseña nueva.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/recuperar_password_mail`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.recuperar_password`

## Objetivo

Recuperar el acceso cuando se ha olvidado la contraseña: envía por correo una contraseña temporal.

## Errores Documentados

- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
