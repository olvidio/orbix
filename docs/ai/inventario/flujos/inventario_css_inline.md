---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "inventario"
titulo: "Qué hace al imprimir"
flujo: "inventario.inventario_css_inline.gestionar.flujo"
preguntas: ["Como qué hace al imprimir en Qué hace al imprimir?"]
pantallas_principales: ["inventario.pantalla.doc_imprimir_ctr", "inventario.pantalla.doc_imprimir_dlb"]
fragmentos: ["inventario.pantalla.doc_imprimir_ctr", "inventario.pantalla.doc_imprimir_dlb"]
endpoints: ["/src/inventario/inventario_css_inline_data"]
source: "docs/catalogo/inventario/flujos/inventario_css_inline.md"
estado_revision: "generado"
---

# Ayuda IA - Qué hace al imprimir

Usa este documento para responder preguntas de usuario sobre como trabajar con `Qué hace al imprimir`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como qué hace al imprimir en Qué hace al imprimir?

## Donde Entrar

- Imprimir inventario centros (`inventario.pantalla.doc_imprimir_ctr`)
- Imprimir inventario DLB (`inventario.pantalla.doc_imprimir_dlb`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Qué hace al imprimir

1. Generar un inventario con **Imprimir ctr**, **Imprimir** o **Imprimir dl**.
2. El formato se aplica solo; no hay nada que configurar.
3. Si la hoja sale mal maquetada, avisar a mantenimiento.

Referencias tecnicas para verificar la respuesta:
- `/src/inventario/inventario_css_inline_data`

## Pantallas Y Fragmentos Relacionados

- `inventario.pantalla.doc_imprimir_ctr`
- `inventario.pantalla.doc_imprimir_dlb`
- `inventario.pantalla.doc_imprimir_ctr`
- `inventario.pantalla.doc_imprimir_dlb`

## Objetivo

Dar formato de impresión (márgenes, cabecera y pie de página) a las hojas de inventario.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
