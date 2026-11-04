---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Cambiar la contraseña"
flujo: "usuarios.usuario_guardar_pwd.gestionar.flujo"
preguntas: ["Como cambiar la contraseña en Cambiar la contraseña?"]
pantallas_principales: ["usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_guardar_pwd"]
source: "docs/catalogo/usuarios/flujos/usuario_guardar_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Cambiar la contraseña

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cambiar la contraseña`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como cambiar la contraseña en Cambiar la contraseña?

## Donde Entrar

- Usuario Form Pwd (`usuarios.pantalla.usuario_form_pwd`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Cambiar la contraseña

1. Escribir la nueva contraseña en **password** y repetirla en **confirma password**.
2. La contraseña debe tener más de 8 caracteres e incluir al menos una mayúscula, un número y un carácter especial (no se admiten comillas dobles).
3. Pulsar **guardar**. Si no coinciden o no es válida aparecen «error: passwords no coincidentes» o «error: password no válido».
4. Al guardar bien se muestra «paswword guardado» y se quita la obligación de cambiarla.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_guardar_pwd`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_pwd`
- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Cambiar la contraseña de un usuario cumpliendo los requisitos de seguridad.

## Errores Documentados

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
