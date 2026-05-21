---
id: "certificados.certificado_emitido_imprimir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Imprimir"
capacidad: "certificados.certificado_emitido_imprimir.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_imprimir"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_imprimir_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Certificado Emitido Imprimir

Propuesta generada automaticamente desde la capacidad `certificados.certificado_emitido_imprimir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CertificadoEmitidoImprimir. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_imprimir`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.destino`
- `form.f_certificado`
- `form.firmado`
- `form.guardar`
- `form.id_item`
- `form.idioma`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_imprimir_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
