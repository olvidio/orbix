---
id: "certificados.certificado_emitido_guardar_pdf.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido Guardar Pdf"
entidades: ["CertificadoEmitidoGuardarMessages"]
acciones: ["ejecutar"]
endpoints: ["/src/certificados/certificado_emitido_guardar_pdf"]
pantallas: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoGuardarMessages"]
tags: ["certificado", "certificado_emitido_guardar_pdf", "certificados", "emitido", "guardar", "pdf"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido Guardar Pdf

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido_guardar_pdf`.

## Objetivo Funcional

Gestiona CertificadoEmitidoGuardarMessages. Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/certificados/certificado_emitido_guardar_pdf`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoEmitidoGuardarMessages`

## Pistas Desde Endpoints

- Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
