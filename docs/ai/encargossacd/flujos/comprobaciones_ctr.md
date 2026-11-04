---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Eliminar encargos de centros inexistentes"
flujo: "encargossacd.comprobaciones_ctr.gestionar.flujo"
preguntas: ["Como eliminar encargos de centros inexistentes en Eliminar encargos de centros inexistentes?"]
pantallas_principales: ["encargossacd.pantalla.comprobaciones"]
fragmentos: ["encargossacd.pantalla.comprobaciones"]
endpoints: ["/src/encargossacd/comprobaciones_ctr"]
source: "docs/catalogo/encargossacd/flujos/comprobaciones_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar encargos de centros inexistentes

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar encargos de centros inexistentes`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar encargos de centros inexistentes en Eliminar encargos de centros inexistentes?

## Donde Entrar

- Comprobaciones (`encargossacd.pantalla.comprobaciones`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar encargos de centros inexistentes

1. Entrar en **listados**.
2. En **comprobaciones** pulsar **Eliminar encargos de centros inexistentes**.
3. Se borran los encargos de centros inactivos y los que no tienen sacerdote.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/comprobaciones_ctr`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.comprobaciones`
- `encargossacd.pantalla.comprobaciones`

## Objetivo

Limpiar los encargos ligados a centros que ya no existen.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
