---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Recuperar 2fa Mail"
flujo: "usuarios.recuperar_2fa_mail.gestionar.flujo"
preguntas: ["Como ejecutar en Recuperar 2fa Mail?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/recuperar_2fa_mail"]
source: "docs/catalogo/usuarios/flujos/recuperar_2fa_mail.md"
estado_revision: "generado"
---

# Ayuda IA - Recuperar 2fa Mail

Usa este documento para responder preguntas de usuario sobre como trabajar con `Recuperar 2fa Mail`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Recuperar 2fa Mail?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Recuperación 2FA: genera código/link y envía mail al usuario.

## Errores Documentados

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
