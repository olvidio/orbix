---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Perm Menu Info"
flujo: "usuarios.perm_menu_info.gestionar.flujo"
preguntas: ["Como ejecutar en Perm Menu Info?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.perm_menu_form"]
endpoints: ["/src/usuarios/perm_menu_info"]
source: "docs/catalogo/usuarios/flujos/perm_menu_info.md"
estado_revision: "generado"
---

# Ayuda IA - Perm Menu Info

Usa este documento para responder preguntas de usuario sobre como trabajar con `Perm Menu Info`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Perm Menu Info?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.perm_menu_form`

## Objetivo

Carga formulario modal de permiso menú (nuevo o edición).

## Errores Documentados

- `Grupo no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
