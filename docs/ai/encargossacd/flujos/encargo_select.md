---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Encargo Select"
flujo: "encargossacd.encargo_select.gestionar.flujo"
preguntas: ["Como obtener datos en Encargo Select?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_select"]
endpoints: ["/src/encargossacd/encargo_select_data"]
source: "docs/catalogo/encargossacd/flujos/encargo_select.md"
estado_revision: "generado"
---

# Ayuda IA - Encargo Select

Usa este documento para responder preguntas de usuario sobre como trabajar con `Encargo Select`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en Encargo Select?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_select`

## Objetivo

Gestiona EncargoSelect. Datos para la lista de encargos (encargo_select). El frontend construye la frontend\shared\web\Lista y los enlaces; aqui devolvemos unicamente los datos planos de cada fila.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
