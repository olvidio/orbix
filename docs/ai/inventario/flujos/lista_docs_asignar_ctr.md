---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Rellenar la asignación a centros"
flujo: "inventario.lista_docs_asignar_ctr.gestionar.flujo"
preguntas: ["Como rellenar la asignación a centros en Rellenar la asignación a centros?"]
pantallas_principales: ["inventario.pantalla.doc_asignar_ctr"]
fragmentos: ["inventario.pantalla.doc_asignar_ctr"]
endpoints: ["/src/inventario/lista_docs_asignar_ctr"]
source: "docs/catalogo/inventario/flujos/lista_docs_asignar_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Rellenar la asignación a centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Rellenar la asignación a centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como rellenar la asignación a centros en Rellenar la asignación a centros?

## Donde Entrar

- Formulario asignar a centros (`inventario.pantalla.doc_asignar_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Rellenar la asignación a centros

1. Revisar el nombre del documento que aparece como título.
2. Escribir en cada centro el **número de registro** (documentos numerados) o el **número de ejemplares**.
3. Indicar las fechas **recibido** y **asignado** (formato dd/mm/aa) si procede.
4. Pulsar **guardar**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/lista_docs_asignar_ctr`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_asignar_ctr`
- `inventario.pantalla.doc_asignar_ctr`

## Objetivo

Indicar, para cada centro elegido, cuántos ejemplares (o qué número de registro) recibe de un documento.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
