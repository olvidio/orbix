---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Imprimir un acta"
flujo: "notas.acta_imprimir_presentacion.gestionar.flujo"
preguntas: ["Como imprimir un acta en Imprimir un acta?"]
pantallas_principales: ["notas.pantalla.acta_imprimir", "notas.pantalla.acta_imprimir_mpdf"]
fragmentos: ["notas.pantalla.acta_imprimir", "notas.pantalla.acta_imprimir_mpdf"]
endpoints: ["/src/notas/acta_imprimir_presentacion_data"]
source: "docs/catalogo/notas/flujos/acta_imprimir_presentacion.md"
estado_revision: "generado"
---

# Ayuda IA - Imprimir un acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `Imprimir un acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como imprimir un acta en Imprimir un acta?

## Donde Entrar

- Acta Imprimir (`notas.pantalla.acta_imprimir`)
- Acta Imprimir Mpdf (`notas.pantalla.acta_imprimir_mpdf`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Imprimir un acta

1. Abrir el acta en **actas**.
2. Pulsar imprimir: aparecen **Cara A (delante)** y **Cara B (detrás)**, o la opción **PDF**.
3. Imprimir antes de subir el PDF firmado: el acta debe imprimirse después de su último cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_imprimir_presentacion_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_imprimir`
- `notas.pantalla.acta_imprimir_mpdf`
- `notas.pantalla.acta_imprimir`
- `notas.pantalla.acta_imprimir_mpdf`

## Objetivo

Preparar el acta para imprimirla (cara A delante y cara B detrás) o sacarla en PDF.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
