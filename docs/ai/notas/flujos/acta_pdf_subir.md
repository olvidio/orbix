---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Subir el PDF firmado"
flujo: "notas.acta_pdf_subir.gestionar.flujo"
preguntas: ["Como subir el pdf firmado en Subir el PDF firmado?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/notas/acta_pdf_subir"]
source: "docs/catalogo/notas/flujos/acta_pdf_subir.md"
estado_revision: "generado"
---

# Ayuda IA - Subir el PDF firmado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Subir el PDF firmado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como subir el pdf firmado en Subir el PDF firmado?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Subir el pdf firmado

1. Imprimir el acta después del último cambio y firmarla.
2. En la ficha, elegir el fichero PDF y subirlo.
3. Si es demasiado grande aparece «El servidor rechazó la subida: archivo o petición demasiado grandes».
4. Desde ese momento el acta queda cerrada y no se puede modificar.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_pdf_subir`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Adjuntar a un acta el PDF firmado.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
