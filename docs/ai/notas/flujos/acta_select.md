---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Buscar un acta"
flujo: "notas.acta_select.gestionar.flujo"
preguntas: ["Como buscar un acta en Buscar un acta?", "Como imprimir o descargar un acta en Buscar un acta?"]
pantallas_principales: ["notas.pantalla.acta_select"]
fragmentos: ["notas.pantalla.acta_select"]
endpoints: ["/src/notas/acta_select_data"]
source: "docs/catalogo/notas/flujos/acta_select.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar un acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar un acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar un acta en Buscar un acta?
- Como imprimir o descargar un acta en Buscar un acta?

## Donde Entrar

- Acta Select (`notas.pantalla.acta_select`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar un acta

1. Entrar en **actas**.
2. Escribir en el buscador el número de acta con formato nn/aa (por ejemplo 12/21). Se pueden usar comodines: un punto (.) es cualquier carácter y .* cualquier cantidad de caracteres (por ejemplo «dlm. 2/19» devuelve dlmE 2/19, dlmO 2/19…).
3. En una delegación solo se buscan las actas de la propia delegación o región; una región con delegaciones busca en todas.
4. Pulsar el acta de la tabla para abrirla.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_select_data`

## Imprimir o descargar un acta

1. En la tabla, marcar el acta.
2. Elegir imprimir o descargar el PDF.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_select_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_select`

## Objetivo

Buscar actas del curso y abrir, modificar, imprimir o descargar su PDF.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
