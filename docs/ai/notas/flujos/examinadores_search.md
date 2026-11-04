---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Elegir un examinador"
flujo: "notas.examinadores_search.gestionar.flujo"
preguntas: ["Como elegir un examinador en Elegir un examinador?"]
pantallas_principales: ["notas.pantalla.acta_ver"]
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/examinadores_search"]
source: "docs/catalogo/notas/flujos/examinadores_search.md"
estado_revision: "generado"
---

# Ayuda IA - Elegir un examinador

Usa este documento para responder preguntas de usuario sobre como trabajar con `Elegir un examinador`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como elegir un examinador en Elegir un examinador?

## Donde Entrar

- Acta Ver (`notas.pantalla.acta_ver`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Elegir un examinador

1. En la ficha del acta, escribir parte del nombre del examinador.
2. Elegir de la lista que aparece.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/examinadores_search`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`
- `notas.pantalla.acta_ver`

## Objetivo

Autocompletar el nombre del examinador al rellenar un acta.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
