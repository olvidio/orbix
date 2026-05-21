---
id: "actividades.tipo_activ_metadata.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Tipo Activ Metadata"
entidades: ["TipoActivMetadata"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/tipo_activ_metadata"]
pantallas: ["frontend/actividades/helpers/ActividadTipo.php", "frontend/actividades/helpers/TipoActivMetadataLoader.php", "frontend/actividades/helpers/TiposDeActividades.php"]
casos_uso: ["src\\actividades\\application\\TipoActivMetadata"]
tags: ["activ", "actividades", "metadata", "tipo", "tipo_activ_metadata"]
estado_revision: "generado"
---

# Gestionar Tipo Activ Metadata

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tipo_activ_metadata`.

## Objetivo Funcional

Gestiona TipoActivMetadata. Endpoint backend que devuelve, en una sola respuesta JSON, los datos que necesita {.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/tipo_activ_metadata`

## Pantallas Relacionadas

- `frontend/actividades/helpers/ActividadTipo.php`
- `frontend/actividades/helpers/TipoActivMetadataLoader.php`
- `frontend/actividades/helpers/TiposDeActividades.php`

## Casos De Uso Detectados

- `src\actividades\application\TipoActivMetadata`

## Pistas Desde Endpoints

- Endpoint backend que devuelve, en una sola respuesta JSON, los datos que necesita {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
