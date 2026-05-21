---
id: "certificados.certificado_emitido.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido"
entidades: ["CertificadoEmitidoGuardarMessages"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/certificados/certificado_emitido_delete", "/src/certificados/certificado_emitido_guardar"]
pantallas: ["frontend/certificados/controller/certificado_emitido_2_mpdf.php", "frontend/certificados/controller/certificado_emitido_imprimir.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoGuardarMessages"]
tags: ["certificado", "certificado_emitido", "certificados", "delete", "emitido", "guardar"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido`.

## Objetivo Funcional

Gestiona CertificadoEmitidoGuardarMessages. Descripcion funcional pendiente de revisar. Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/certificados/certificado_emitido_delete`
- `/src/certificados/certificado_emitido_guardar`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_2_mpdf.php`
- `frontend/certificados/controller/certificado_emitido_imprimir.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoEmitidoGuardarMessages`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.
- Mensajes legibles al guardar un certificado emitido (errores de BD, etc.).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
