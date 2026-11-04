---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "certificados"
titulo: "Obtener el PDF del certificado"
flujo: "certificados.certificado_emitido_imprimir_mpdf.gestionar.flujo"
preguntas: ["Como obtener el pdf del certificado en Obtener el PDF del certificado?"]
pantallas_principales: ["certificados.pantalla.certificado_emitido_imprimir_mpdf"]
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir_mpdf"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_mpdf_datos"]
source: "docs/catalogo/certificados/flujos/certificado_emitido_imprimir_mpdf.md"
estado_revision: "generado"
---

# Ayuda IA - Obtener el PDF del certificado

Usa este documento para responder preguntas de usuario sobre como trabajar con `Obtener el PDF del certificado`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener el pdf del certificado en Obtener el PDF del certificado?

## Donde Entrar

- Certificado Emitido Imprimir Mpdf (`certificados.pantalla.certificado_emitido_imprimir_mpdf`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener el pdf del certificado

1. Emitir el certificado desde la ficha del alumno.
2. El PDF se genera automáticamente y se abre para imprimir o guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/certificados/certificado_emitido_imprimir_mpdf_datos`

## Pantallas Y Fragmentos Relacionados

- `certificados.pantalla.certificado_emitido_imprimir_mpdf`
- `certificados.pantalla.certificado_emitido_imprimir_mpdf`

## Objetivo

Generar el PDF del certificado con las notas y los textos traducidos al idioma elegido.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
