---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "el PDF del acta"
flujo: "notas.acta_pdf.gestionar.flujo"
preguntas: ["Como gestionar el pdf del acta en el PDF del acta?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/notas/acta_pdf_eliminar"]
source: "docs/catalogo/notas/flujos/acta_pdf.md"
estado_revision: "generado"
---

# Ayuda IA - el PDF del acta

Usa este documento para responder preguntas de usuario sobre como trabajar con `el PDF del acta`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar el pdf del acta en el PDF del acta?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar el pdf del acta

1. Subir: elegir el PDF firmado y subirlo (el acta debe haberse impreso tras el último cambio).
2. Descargar: pulsar descargar PDF.
3. Eliminar: quitar el PDF para poder modificar el acta de nuevo.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_pdf_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestionar el PDF escaneado del acta: subirlo, descargarlo o eliminarlo.

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
