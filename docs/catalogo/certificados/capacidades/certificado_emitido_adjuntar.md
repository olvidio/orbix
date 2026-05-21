---
id: "certificados.certificado_emitido_adjuntar.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Emitido Adjuntar"
entidades: ["CertificadoEmitidoAdjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_emitido_adjuntar_data"]
pantallas: ["frontend/certificados/controller/certificado_emitido_adjuntar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoEmitidoAdjuntarFormData"]
tags: ["adjuntar", "certificado", "certificado_emitido_adjuntar", "certificados", "data", "emitido"]
estado_revision: "generado"
---

# Gestionar Certificado Emitido Adjuntar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_emitido_adjuntar`.

## Objetivo Funcional

Gestiona CertificadoEmitidoAdjuntar. Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificado_emitido_adjuntar_data`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_adjuntar.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoEmitidoAdjuntarFormData`

## Pistas Desde Endpoints

- Datos para el formulario “adjuntar certificado emitido” (solo lectura inicial).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
