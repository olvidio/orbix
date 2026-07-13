---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Usuario Info"
flujo: "usuarios.usuario_info.gestionar.flujo"
preguntas: ["Como ejecutar en Usuario Info?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_info"]
source: "docs/catalogo/usuarios/flujos/usuario_info.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Info

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Info`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Usuario Info?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Resumen usuario para cabecera ficha (grupos, login, email).

## Errores Documentados

- `Id de usuario no válido`
- `Usuario no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
