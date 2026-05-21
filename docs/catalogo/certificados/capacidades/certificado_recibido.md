---
id: "certificados.certificado_recibido.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificado Recibido"
entidades: ["CertificadoRecibido"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/certificados/certificado_recibido_delete", "/src/certificados/certificado_recibido_guardar"]
pantallas: ["frontend/certificados/controller/certificado_recibido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_modificar.php"]
casos_uso: []
tags: ["certificado", "certificado_recibido", "certificados", "delete", "guardar", "recibido"]
estado_revision: "generado"
---

# Gestionar Certificado Recibido

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificado_recibido`.

## Objetivo Funcional

Gestiona CertificadoRecibido. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/certificados/certificado_recibido_delete`
- `/src/certificados/certificado_recibido_guardar`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_recibido_adjuntar.php`
- `frontend/certificados/controller/certificado_recibido_modificar.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
