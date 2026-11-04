---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Abrir las preferencias"
flujo: "usuarios.usuario_preferencias.gestionar.flujo"
preguntas: ["Como abrir las preferencias en Abrir las preferencias?"]
pantallas_principales: ["usuarios.pantalla.preferencias"]
fragmentos: ["usuarios.pantalla.preferencias"]
endpoints: ["/src/usuarios/usuario_preferencias"]
source: "docs/catalogo/usuarios/flujos/usuario_preferencias.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir las preferencias

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir las preferencias`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir las preferencias en Abrir las preferencias?

## Donde Entrar

- Preferencias (`usuarios.pantalla.preferencias`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir las preferencias

1. Abrir **preferencias** en el menú de usuario.
2. La pantalla muestra los valores que ya tiene guardados el usuario.

Referencias tecnicas para verificar la respuesta:
- `/src/usuarios/usuario_preferencias`

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.preferencias`
- `usuarios.pantalla.preferencias`

## Objetivo

Cargar los valores actuales de las preferencias personales al abrir la pantalla.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
