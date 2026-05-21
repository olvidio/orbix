---
id: "certificados.certificados_locales.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificados Locales"
capacidad: "certificados.certificados_locales.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_adjuntar", "certificados.pantalla.certificado_recibido_adjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificados_locales_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Certificados Locales

Propuesta generada automaticamente desde la capacidad `certificados.certificados_locales.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CertificadosLocales. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_adjuntar`
- `certificados.pantalla.certificado_recibido_adjuntar`

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
- `form.f_recibido`
- `form.firmado`
- `form.idioma`
- `post.id_nom`
- `post.nuevo`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificados_locales_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
