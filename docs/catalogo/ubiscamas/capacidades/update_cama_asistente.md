---
id: "ubiscamas.update_cama_asistente.gestionar"
tipo: "capacidad"
modulo: "ubiscamas"
nombre: "Gestionar Update Cama Asistente"
entidades: ["AsistenteActividadService"]
acciones: ["ejecutar"]
endpoints: ["/src/ubiscamas/update_cama_asistente"]
pantallas: []
casos_uso: ["src\\asistentes\\application\\services\\AsistenteActividadService"]
tags: ["asistente", "cama", "ubiscamas", "update", "update_cama_asistente"]
estado_revision: "generado"
---

# Gestionar Update Cama Asistente

Propuesta generada automaticamente a partir de endpoints con prefijo comun `update_cama_asistente`.

## Objetivo Funcional

Gestiona AsistenteActividadService. Servicio de aplicación para operaciones de asistentes que requieren coordinación entre múltiples repositorios.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/ubiscamas/update_cama_asistente`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\asistentes\application\services\AsistenteActividadService`

## Pistas Desde Endpoints

- Servicio de aplicación para operaciones de asistentes que requieren coordinación entre múltiples repositorios

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
