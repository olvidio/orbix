---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Primer acceso sin 2FA"
flujo: "usuarios.check_first_login_2fa.gestionar.flujo"
preguntas: ["Como primer acceso sin 2fa en Primer acceso sin 2FA?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/check_first_login_2fa"]
source: "docs/catalogo/usuarios/flujos/check_first_login_2fa.md"
estado_revision: "generado"
---

# Ayuda IA - Primer acceso sin 2FA

Usa este documento para responder preguntas de usuario sobre como trabajar con `Primer acceso sin 2FA`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como primer acceso sin 2fa en Primer acceso sin 2FA?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Primer acceso sin 2fa

1. Iniciar sesión con login y contraseña.
2. Si el usuario no tiene la verificación en dos pasos activa, se le lleva a la pantalla de configuración.
3. Si ya la tiene, entra directamente a la página de inicio.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/check_first_login_2fa`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Tras iniciar sesión, llevar al usuario a configurar la verificación en dos pasos si aún no la tiene.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
