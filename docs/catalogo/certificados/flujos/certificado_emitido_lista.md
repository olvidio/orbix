---
id: "certificados.certificado_emitido_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "certificados"
nombre: "Flujo - Gestionar Certificado Emitido Lista"
capacidad: "certificados.certificado_emitido_lista.gestionar"
pantallas_principales: []
fragmentos: ["certificados.pantalla.certificado_emitido_lista"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_lista_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Certificado Emitido Lista

Propuesta generada automaticamente desde la capacidad `certificados.certificado_emitido_lista.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CertificadoEmitidoLista. Esta página muestra una tabla con los certificados.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `certificados.pantalla.certificado_emitido_lista`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.certificado`
- `form.mod`
- `form.sel`
- `html.btn_ok`
- `html.certificado`
- `html.mod`
- `html.refresh`
- `post.certificado`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_certificado`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_certificado`

## Endpoints Del Flujo

- `/src/certificados/certificado_emitido_lista_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
