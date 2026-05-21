---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Comprobaciones Ctr"
flujo: "encargossacd.comprobaciones_ctr.gestionar.flujo"
preguntas: ["Como ejecutar en Comprobaciones Ctr?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.comprobaciones"]
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
source: "docs/catalogo/encargossacd/flujos/comprobaciones_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Comprobaciones Ctr

Usa este documento para responder preguntas de usuario sobre como trabajar con `Comprobaciones Ctr`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ejecutar en Comprobaciones Ctr?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ejecutar

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.comprobaciones`

## Objetivo

Gestiona EncargoComprobacionesCtr. Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo frontend/encargossacd/controller/comprobaciones.php).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
