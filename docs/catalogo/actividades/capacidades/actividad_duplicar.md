---
id: "actividades.actividad_duplicar.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Duplicar"
entidades: ["ActividadDuplicar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_duplicar"]
pantallas: []
casos_uso: []
tags: ["actividad", "actividad_duplicar", "actividades", "duplicar"]
estado_revision: "generado"
---

# Gestionar Actividad Duplicar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_duplicar`.

## Objetivo Funcional

Gestiona ActividadDuplicar. Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de la propia delegacion (o de la sf si el usuario tiene permiso des).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/actividad_duplicar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Endpoint backend AJAX: duplica la primera actividad seleccionada dentro de la propia delegacion (o de la sf si el usuario tiene permiso `des`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
