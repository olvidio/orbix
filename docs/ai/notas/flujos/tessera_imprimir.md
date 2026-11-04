---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Imprimir la tessera"
flujo: "notas.tessera_imprimir.gestionar.flujo"
preguntas: ["Como imprimir la tessera en Imprimir la tessera?"]
pantallas_principales: ["notas.pantalla.tessera_imprimir"]
fragmentos: ["notas.pantalla.tessera_imprimir", "notas.pantalla.tessera_imprimir_mpdf"]
endpoints: ["/src/notas/tessera_imprimir_data"]
source: "docs/catalogo/notas/flujos/tessera_imprimir.md"
estado_revision: "generado"
---

# Ayuda IA - Imprimir la tessera

Usa este documento para responder preguntas de usuario sobre como trabajar con `Imprimir la tessera`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como imprimir la tessera en Imprimir la tessera?

## Donde Entrar

- Tessera Imprimir (`notas.pantalla.tessera_imprimir`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Imprimir la tessera

1. Abrir la tessera de la persona.
2. Pulsar imprimir (HTML o PDF).

Referencias tecnicas para verificar la respuesta:
- `/src/notas/tessera_imprimir_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.tessera_imprimir`
- `notas.pantalla.tessera_imprimir`
- `notas.pantalla.tessera_imprimir_mpdf`

## Objetivo

Imprimir la tessera de estudios de una persona.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
