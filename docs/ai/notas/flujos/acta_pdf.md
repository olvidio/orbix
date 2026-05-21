---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta Pdf"
flujo: "notas.acta_pdf.gestionar.flujo"
preguntas: ["Como eliminar en Acta Pdf?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/notas/acta_pdf_eliminar"]
source: "docs/catalogo/notas/flujos/acta_pdf.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Pdf

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Pdf`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar en Acta Pdf?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_pdf_eliminar`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Gestiona ActaPdf. Elimina el PDF firmado asociado a un Acta (sin borrar el acta).

## Errores Documentados

- `No se encuentra el acta`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
