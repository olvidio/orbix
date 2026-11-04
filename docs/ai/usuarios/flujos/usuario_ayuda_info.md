---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Consultar el correo asociado"
flujo: "usuarios.usuario_ayuda_info.gestionar.flujo"
preguntas: ["Como consultar el correo asociado en Consultar el correo asociado?"]
pantallas_principales: ["usuarios.pantalla.ayuda_2fa_reset", "usuarios.pantalla.ayuda_acceso"]
fragmentos: []
endpoints: ["/src/usuarios/usuario_ayuda_info"]
source: "docs/catalogo/usuarios/flujos/usuario_ayuda_info.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar el correo asociado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar el correo asociado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar el correo asociado en Consultar el correo asociado?

## Donde Entrar

- Ayuda 2fa Reset (`usuarios.pantalla.ayuda_2fa_reset`)
- Ayuda Acceso (`usuarios.pantalla.ayuda_acceso`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar el correo asociado

1. Abrir la ayuda de acceso.
2. Escribir el login válido.
3. Se muestra el correo parcialmente oculto al que llegarán los avisos y el contacto del administrador.
4. Si el login no es válido aparece «Debe ingresar un nombre de usuario válido».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_ayuda_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.ayuda_2fa_reset`
- `usuarios.pantalla.ayuda_acceso`

## Objetivo

En la pantalla de ayuda de acceso, mostrar el correo (ofuscado) asociado al login y el contacto del administrador regional.

## Errores Documentados

- `Esquema no válido`
- `Debe ingresar un nombre de usuario válido`
- `No hay email asociado a este usuario`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
