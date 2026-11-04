---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Validar una contraseña al escribirla"
flujo: "usuarios.usuario_check_pwd.gestionar.flujo"
preguntas: ["Como validar una contraseña al escribirla en Validar una contraseña al escribirla?"]
pantallas_principales: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_check_pwd"]
source: "docs/catalogo/usuarios/flujos/usuario_check_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Validar una contraseña al escribirla

Usa este documento para responder preguntas de usuario sobre como trabajar con `Validar una contraseña al escribirla`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como validar una contraseña al escribirla en Validar una contraseña al escribirla?

## Donde Entrar

- Usuario Form (`usuarios.pantalla.usuario_form`)
- Usuario Form Pwd (`usuarios.pantalla.usuario_form_pwd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Validar una contraseña al escribirla

1. Escribir la contraseña en el formulario.
2. Al pulsar guardar se comprueba automáticamente.
3. Si no cumple, sale un aviso «password error» con el motivo y no se guarda. La regla: más de 8 caracteres e incluir al menos una letra mayúscula, un número y un carácter especial. Ejemplos: «abc» falla por corta; «abcdefgh1» falla por no tener mayúscula ni carácter especial.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_check_pwd`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_pwd`
- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Comprobar, antes de guardar, que la contraseña escrita cumple los requisitos.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
