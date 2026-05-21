---
id: "certificados.certificado_recibido_modificar.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Recibido Modificar"
entidades: ["CertificadoRecibidoModificar"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificado_recibido_modificar_data"]
pantallas: ["frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: ["src\\certificados\\application\\CertificadoRecibidoModificarFormData"]
tags: ["certificado", "certificado_recibido_modificar", "certificados", "data", "modificar", "recibido"]
estado_revision: "generado"
---

# Gestionar Certificado Recibido Modificar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_recibido_modificar`.

## Objetivo Funcional

Gestiona CertificadoRecibidoModificar. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificado_recibido_modificar_data`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_recibido_modificar.php`

## Casos De Uso Detectados

- `src\certificados\application\CertificadoRecibidoModificarFormData`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
