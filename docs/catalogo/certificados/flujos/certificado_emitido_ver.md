---
id: "certificados.certificado_emitido_ver.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Ver"
capacidad: "certificados.certificado_emitido_ver.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_ver_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Certificado Emitido Ver

Propuesta generada automaticamente desde la capacidad `certificados.certificado_emitido_ver.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CertificadoEmitidoVer. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_ver`

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
- `form.destino`
- `form.f_certificado`
- `form.f_enviado`
- `form.firmado`
- `form.idioma`
- `form.nom`
- `post.sel`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_ver_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
