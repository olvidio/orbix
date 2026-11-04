---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Ver el resumen de un usuario en su ficha"
flujo: "usuarios.usuario_info.gestionar.flujo"
preguntas: ["Como ver el resumen de un usuario en su ficha en Ver el resumen de un usuario en su ficha?"]
pantallas_principales: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_info"]
source: "docs/catalogo/usuarios/flujos/usuario_info.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el resumen de un usuario en su ficha

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el resumen de un usuario en su ficha`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el resumen de un usuario en su ficha en Ver el resumen de un usuario en su ficha?

## Donde Entrar

- Usuario Form (`usuarios.pantalla.usuario_form`)
- Usuario Form 2fa (`usuarios.pantalla.usuario_form_2fa`)
- Usuario Form Mail (`usuarios.pantalla.usuario_form_mail`)
- Usuario Form Pwd (`usuarios.pantalla.usuario_form_pwd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el resumen de un usuario en su ficha

1. Abrir la ficha de un usuario desde **lista usuarios**.
2. La cabecera muestra el login, el correo y los grupos asignados; no hay que hacer nada más.
3. Si el usuario no existe aparece «Usuario no encontrado».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`
- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Calcular el resumen del usuario (grupos, login, correo) que se muestra en la cabecera de su ficha.

## Errores Documentados

- `Id de usuario no válido`
- `Usuario no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
