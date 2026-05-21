---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas D"
flujo: "encargossacd.listas_d.gestionar.flujo"
preguntas: ["Como obtener datos en Listas D?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_d"]
endpoints: ["/src/encargossacd/listas_d_data"]
source: "docs/catalogo/encargossacd/flujos/listas_d.md"
estado_revision: "generado"
---

# Ayuda IA - Listas D

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas D`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Listas D?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_d`

## Objetivo

Gestiona ListasD. Genera el listado "d" de atencion SACD (cr 9/20, 10). Sustituye la logica de frontend/encargossacd/controller/listas_d.php. La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en Html para que el frontend solo tenga que volcarlas al cliente.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
