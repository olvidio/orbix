---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Imprimir el inventario de la dlb/casa"
flujo: "inventario.inventario_dlb.gestionar.flujo"
preguntas: ["Como imprimir el inventario de la dlb/casa en Imprimir el inventario de la dlb/casa?"]
pantallas_principales: ["inventario.pantalla.doc_imprimir_dlb"]
fragmentos: ["inventario.pantalla.doc_imprimir_dlb"]
endpoints: ["/src/inventario/inventario_dlb"]
source: "docs/catalogo/inventario/flujos/inventario_dlb.md"
estado_revision: "generado"
---

# Ayuda IA - Imprimir el inventario de la dlb/casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Imprimir el inventario de la dlb/casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como imprimir el inventario de la dlb/casa en Imprimir el inventario de la dlb/casa?

## Donde Entrar

- Imprimir inventario DLB (`inventario.pantalla.doc_imprimir_dlb`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Imprimir el inventario de la dlb/casa

1. Marcar los lugares en **inventario de dlb/casa**.
2. Pulsar **Imprimir** (o **Imprimir dl** para añadir la columna **observaciones dl**).
3. Se muestra una hoja por lugar titulada **Inventario de documentos de** (centro y lugar), con **documento**, **id** y **observaciones**.
4. El botón **seleccionar** y la primera columna (sin cabecera) de la hoja sirven solo para imprimir y marcar a mano en papel.
5. Imprimir desde el navegador. Cada hoja termina con «(*) Se guarda bajo llave», el total de páginas y el espacio **Conforme (firma)**.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/inventario_dlb`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_imprimir_dlb`
- `inventario.pantalla.doc_imprimir_dlb`

## Objetivo

Obtener el inventario impreso de los documentos de uno o varios lugares de la dlb/casa, listo para firmar.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
