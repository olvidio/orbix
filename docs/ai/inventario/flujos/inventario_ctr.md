---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Imprimir el inventario de centros"
flujo: "inventario.inventario_ctr.gestionar.flujo"
preguntas: ["Como imprimir el inventario de centros en Imprimir el inventario de centros?"]
pantallas_principales: ["inventario.pantalla.doc_imprimir_ctr"]
fragmentos: ["inventario.pantalla.doc_imprimir_ctr"]
endpoints: ["/src/inventario/inventario_ctr"]
source: "docs/catalogo/inventario/flujos/inventario_ctr.md"
estado_revision: "generado"
---

# Ayuda IA - Imprimir el inventario de centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Imprimir el inventario de centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como imprimir el inventario de centros en Imprimir el inventario de centros?

## Donde Entrar

- Imprimir inventario centros (`inventario.pantalla.doc_imprimir_ctr`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Imprimir el inventario de centros

1. Marcar los centros en **inventario de centros**.
2. Pulsar **Imprimir ctr** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por centro titulada **Inventario de documentos de** (nombre del centro), con **documento** y **observaciones**.
4. Imprimir desde el navegador. Cada hoja termina con la nota «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/inventario_ctr`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_imprimir_ctr`
- `inventario.pantalla.doc_imprimir_ctr`

## Objetivo

Obtener el inventario impreso de los documentos de uno o varios centros, listo para firmar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
