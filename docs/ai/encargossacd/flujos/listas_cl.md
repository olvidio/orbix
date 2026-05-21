---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas Cl"
flujo: "encargossacd.listas_cl.gestionar.flujo"
preguntas: ["Como obtener datos en Listas Cl?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_cl"]
endpoints: ["/src/encargossacd/listas_cl_data"]
source: "docs/catalogo/encargossacd/flujos/listas_cl.md"
estado_revision: "generado"
---

# Ayuda IA - Listas Cl

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas Cl`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Listas Cl?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_cl`

## Objetivo

Gestiona ListasCl. Listado de cl para cr, restringido a los centros de la sss+. Sustituye la logica de frontend/encargossacd/controller/listas_cl.php (era una plantilla con SQL crudo). Devuelve el HTML completo listo para volcarlo al cliente; el frontend se limita a pasar sf y a echo del resultado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
