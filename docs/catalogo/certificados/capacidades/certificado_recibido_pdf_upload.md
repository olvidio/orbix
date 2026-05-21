---
id: "certificados.certificado_recibido_pdf_upload.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Recibido Pdf Upload"
entidades: ["CertificadoRecibidoPdfUpload"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_recibido_pdf_upload"]
pantallas: ["frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_modificar.php", "frontend/certificados/controller/certificado_recibido_pdf_upload.php"]
casos_uso: []
tags: ["certificado", "certificado_recibido_pdf_upload", "certificados", "pdf", "recibido", "upload"]
estado_revision: "generado"
---

# Gestionar Certificado Recibido Pdf Upload

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_recibido_pdf_upload`.

## Objetivo Funcional

Gestiona CertificadoRecibidoPdfUpload. Subida AJAX del PDF (certificado recibido, FormData multipart).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/certificados/certificado_recibido_pdf_upload`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- `frontend/certificados/controller/certificado_recibido_modificar.php`
- `frontend/certificados/controller/certificado_recibido_pdf_upload.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Subida AJAX del PDF (certificado recibido, FormData multipart).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
