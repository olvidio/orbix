---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Entrar desde la app móvil"
flujo: "usuarios.app_login.gestionar.flujo"
preguntas: ["Como entrar desde la app móvil en Entrar desde la app móvil?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/app_login"]
source: "docs/catalogo/usuarios/flujos/app_login.md"
estado_revision: "generado"
---

# Ayuda IA - Entrar desde la app móvil

Usa este documento para responder preguntas de usuario sobre como trabajar con `Entrar desde la app móvil`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como entrar desde la app móvil en Entrar desde la app móvil?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Entrar desde la app móvil

1. Abrir la app e indicar usuario, contraseña y esquema.
2. Si el usuario tiene 2FA, escribir el código de verificación.
3. Si falta usuario o contraseña aparece «Usuario y contraseña obligatorios».

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/app_login`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Iniciar sesión desde la aplicación móvil con usuario, contraseña y, si procede, código 2FA.

## Errores Documentados

- `Usuario y contraseña obligatorios`
- `Esquema no indicado`
- `Esquema no válido`
- `Error de autenticación`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
