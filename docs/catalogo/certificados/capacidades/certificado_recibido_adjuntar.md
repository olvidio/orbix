---
id: "certificados.certificado_recibido_adjuntar.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Recibido Adjuntar"
entidades: ["CertificadoRecibidoAdjuntar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_adjuntar_data"]
pantallas: ["frontend/certificados/controller/certificado_recibido_adjuntar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoRecibidoAdjuntarFormData"]
tags: ["adjuntar", "certificado", "certificado_recibido_adjuntar", "certificados", "data", "recibido"]
estado_revision: "generado"
---

# Gestionar Certificado Recibido Adjuntar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_recibido_adjuntar`.

## Objetivo Funcional

Gestiona CertificadoRecibidoAdjuntar. Datos para el formulario «adjuntar certificado recibido» (solo lectura inicial).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificado_recibido_adjuntar_data`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoRecibidoAdjuntarFormData`

## Pistas Desde Endpoints

- Datos para el formulario «adjuntar certificado recibido» (solo lectura inicial).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
