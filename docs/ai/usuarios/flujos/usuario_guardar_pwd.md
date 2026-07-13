---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Usuario Guardar Pwd"
flujo: "usuarios.usuario_guardar_pwd.gestionar.flujo"
preguntas: ["Como ejecutar en Usuario Guardar Pwd?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form_pwd"]
endpoints: ["/src/usuarios/usuario_guardar_pwd"]
source: "docs/catalogo/usuarios/flujos/usuario_guardar_pwd.md"
estado_revision: "generado"
---

# Ayuda IA - Usuario Guardar Pwd

Usa este documento para responder preguntas de usuario sobre como trabajar con `Usuario Guardar Pwd`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Usuario Guardar Pwd?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.usuario_form_pwd`

## Objetivo

Cambia contraseña tras validar fortaleza; limpia flag cambio_password.

## Errores Documentados

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
