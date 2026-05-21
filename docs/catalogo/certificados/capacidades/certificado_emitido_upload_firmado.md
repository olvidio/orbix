---
id: "certificados.certificado_emitido_upload_firmado.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido Upload Firmado"
entidades: ["CertificadoEmitidoUploadFirmado"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_upload_firmado_data"]
pantallas: ["frontend/certificados/controller/certificado_emitido_upload_firmado.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoUploadFirmadoFormData"]
tags: ["certificado", "certificado_emitido_upload_firmado", "certificados", "data", "emitido", "firmado", "upload"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido Upload Firmado

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido_upload_firmado`.

## Objetivo Funcional

Gestiona CertificadoEmitidoUploadFirmado. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificado_emitido_upload_firmado_data`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_upload_firmado.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoEmitidoUploadFirmadoFormData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
