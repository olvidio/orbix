---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Usuario Check Pwd"
flujo: "usuarios.usuario_check_pwd.gestionar.flujo"
preguntas: ["Como ejecutar en Usuario Check Pwd?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_check_pwd"]
source: "docs/catalogo/usuarios/flujos/usuario_check_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Check Pwd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Check Pwd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Usuario Check Pwd?

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
- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Valida fortaleza de contraseña (JsonResponse directo, no envelope ContestarJson).

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
