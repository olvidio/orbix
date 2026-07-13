---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "usuarios"
titulo: "Grupo Info"
flujo: "usuarios.grupo_info.gestionar.flujo"
preguntas: ["Como ejecutar en Grupo Info?"]
pantallas_principales: []
fragmentos: ["usuarios.pantalla.grupo_form"]
endpoints: ["/src/usuarios/grupo_info"]
source: "docs/catalogo/usuarios/flujos/grupo_info.md"
estado_revision: "generado"
---

# Ayuda IA - Grupo Info

Usa este documento para responder preguntas de usuario sobre como trabajar con `Grupo Info`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Grupo Info?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `usuarios.pantalla.grupo_form`

## Objetivo

Devuelve el nombre de un grupo para el formulario de edición.

## Errores Documentados

- `Grupo no encontrado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
