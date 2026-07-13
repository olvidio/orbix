---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Recuperar Password Mail"
flujo: "usuarios.recuperar_password_mail.gestionar.flujo"
preguntas: ["Como ejecutar en Recuperar Password Mail?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/usuarios/recuperar_password_mail"]
source: "docs/catalogo/usuarios/flujos/recuperar_password_mail.md"
estado_revision: "generado"
---

# Ayuda IA - Recuperar Password Mail

Usa este documento para responder preguntas de usuario sobre como trabajar con `Recuperar Password Mail`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Recuperar Password Mail?

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

Recuperación contraseña: genera pwd temporal, marca cambio obligatorio y envía mail.

## Errores Documentados

- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
