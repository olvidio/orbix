---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Rellenar la asignación a la dlb/casa"
flujo: "inventario.lista_docs_asignar_dlb.gestionar.flujo"
preguntas: ["Como rellenar la asignación a la dlb/casa en Rellenar la asignación a la dlb/casa?"]
pantallas_principales: ["inventario.pantalla.doc_asignar_dlb"]
fragmentos: ["inventario.pantalla.doc_asignar_dlb"]
endpoints: ["/src/inventario/lista_docs_asignar_dlb"]
source: "docs/catalogo/inventario/flujos/lista_docs_asignar_dlb.md"
estado_revision: "generado"
---

# Ayuda IA - Rellenar la asignación a la dlb/casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Rellenar la asignación a la dlb/casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como rellenar la asignación a la dlb/casa en Rellenar la asignación a la dlb/casa?

## Donde Entrar

- Formulario asignar DLB (`inventario.pantalla.doc_asignar_dlb`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Rellenar la asignación a la dlb/casa

1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada **delegación** el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** si procede.
4. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_asignar_dlb`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_asignar_dlb`
- `inventario.pantalla.doc_asignar_dlb`

## Objetivo

Indicar, para cada lugar de la dlb/casa elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
