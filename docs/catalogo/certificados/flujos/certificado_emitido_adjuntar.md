---
id: "certificados.certificado_emitido_adjuntar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Adjuntar"
capacidad: "certificados.certificado_emitido_adjuntar.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_adjuntar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Certificado Emitido Adjuntar

Propuesta generada automaticamente desde la capacidad `certificados.certificado_emitido_adjuntar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CertificadoEmitidoAdjuntar. Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_adjuntar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.certificado_pdf`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_adjuntar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
