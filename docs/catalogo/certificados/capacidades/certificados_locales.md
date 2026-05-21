---
id: "certificados.certificados_locales.gestionar"
tipo: "capacidad"
modulo: "certificados"
nombre: "Gestionar Certificados Locales"
entidades: ["CertificadosLocales"]
acciones: ["obtener_datos"]
endpoints: ["/src/certificados/certificados_locales_data"]
pantallas: ["frontend/certificados/controller/certificado_emitido_adjuntar.php", "frontend/certificados/controller/certificado_recibido_adjuntar.php"]
casos_uso: []
tags: ["certificados", "certificados_locales", "data", "locales"]
estado_revision: "generado"
---

# Gestionar Certificados Locales

Propuesta generada automaticamente a partir de endpoints con prefijo comun `certificados_locales`.

## Objetivo Funcional

Gestiona CertificadosLocales. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/certificados/certificados_locales_data`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_adjuntar.php`
- `frontend/certificados/controller/certificado_recibido_adjuntar.php`

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
