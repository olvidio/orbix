---
id: "certificados.certificado_emitido_pdf_upload.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido Pdf Upload"
entidades: ["CertificadoEmitidoPdfUpload"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_pdf_upload"]
pantallas: ["frontend/certificados/controller/certificado_emitido_pdf_upload.php"]
casos_uso: []
tags: ["certificado", "certificado_emitido_pdf_upload", "certificados", "emitido", "pdf", "upload"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido Pdf Upload

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido_pdf_upload`.

## Objetivo Funcional

Gestiona CertificadoEmitidoPdfUpload. Subida AJAX del PDF (bootstrap-fileinput / FormData multipart).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/certificados/certificado_emitido_pdf_upload`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_pdf_upload.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Subida AJAX del PDF (bootstrap-fileinput / FormData multipart).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
