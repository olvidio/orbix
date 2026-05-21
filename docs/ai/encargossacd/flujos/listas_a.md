---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas A"
flujo: "encargossacd.listas_a.gestionar.flujo"
preguntas: ["Como obtener datos en Listas A?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_a"]
endpoints: ["/src/encargossacd/listas_a_data"]
source: "docs/catalogo/encargossacd/flujos/listas_a.md"
estado_revision: "generado"
---

# Ayuda IA - Listas A

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas A`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Listas A?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_a`

## Objetivo

Gestiona ListasA. Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en frontend/encargossacd/controller/listas_a.php. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista listas.phtml.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
