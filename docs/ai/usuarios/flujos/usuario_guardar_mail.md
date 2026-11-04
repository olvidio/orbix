---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Cambiar el correo"
flujo: "usuarios.usuario_guardar_mail.gestionar.flujo"
preguntas: ["Como cambiar el correo en Cambiar el correo?"]
pantallas_principales: ["usuarios.pantalla.usuario_form_mail"]
fragmentos: []
endpoints: ["/src/usuarios/usuario_guardar_mail"]
source: "docs/catalogo/usuarios/flujos/usuario_guardar_mail.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar el correo

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar el correo`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar el correo en Cambiar el correo?

## Donde Entrar

- Usuario Form Mail (`usuarios.pantalla.usuario_form_mail`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar el correo

1. Abrir la ficha del usuario (o las preferencias personales).
2. Escribir el nuevo correo en **Email** y pulsar **guardar**.
3. El correo se usa para la recuperación de contraseña y de la verificación en dos pasos.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_guardar_mail`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_mail`

## Objetivo

Cambiar el correo electrónico de un usuario.

## Errores Documentados

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
