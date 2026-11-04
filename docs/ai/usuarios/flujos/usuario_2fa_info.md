---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Ver el estado y el QR"
flujo: "usuarios.usuario_2fa_info.gestionar.flujo"
preguntas: ["Como ver el estado y el qr en Ver el estado y el QR?"]
pantallas_principales: ["usuarios.pantalla.usuario_form_2fa"]
fragmentos: ["usuarios.pantalla.usuario_form_2fa"]
endpoints: ["/src/usuarios/usuario_2fa_info"]
source: "docs/catalogo/usuarios/flujos/usuario_2fa_info.md"
estado_revision: "generado"
---

# Ayuda IA - Ver el estado y el QR

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver el estado y el QR`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver el estado y el qr en Ver el estado y el QR?

## Donde Entrar

- Usuario Form 2fa (`usuarios.pantalla.usuario_form_2fa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver el estado y el qr

1. Abrir la configuración de **autenticación de dos factores (2FA)**.
2. Se muestra si está activada y, si no, el **Código QR** y la **Clave secreta** para configurarla.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_2fa_info`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_2fa`

## Objetivo

Mostrar el estado de la verificación en dos pasos del usuario y los datos para configurarla.

## Errores Documentados

- `Id de usuario no válido`
- `Usuario no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
