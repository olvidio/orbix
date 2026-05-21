---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas B"
flujo: "encargossacd.listas_b.gestionar.flujo"
preguntas: ["Como obtener datos en Listas B?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_b"]
endpoints: ["/src/encargossacd/listas_b_data"]
source: "docs/catalogo/encargossacd/flujos/listas_b.md"
estado_revision: "generado"
---

# Ayuda IA - Listas B

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas B`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Listas B?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_b`

## Objetivo

Gestiona ListasB. Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de frontend/encargossacd/controller/listas_b.php.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
