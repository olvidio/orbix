---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Reabrir la app con sesión activa"
flujo: "usuarios.app_session.gestionar.flujo"
preguntas: ["Como reabrir la app con sesión activa en Reabrir la app con sesión activa?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/app_session"]
source: "docs/catalogo/usuarios/flujos/app_session.md"
estado_revision: "generado"
---

# Ayuda IA - Reabrir la app con sesión activa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Reabrir la app con sesión activa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como reabrir la app con sesión activa en Reabrir la app con sesión activa?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Reabrir la app con sesión activa

1. Abrir la app.
2. Si la sesión sigue vigente entra directamente; si no, muestra la pantalla de acceso.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/app_session`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Al arrancar la app móvil, comprobar si ya hay una sesión abierta para no pedir de nuevo las credenciales.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
