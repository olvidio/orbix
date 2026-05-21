---
id: "actividades.actividad_importar.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Importar"
entidades: ["ActividadImportar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_importar"]
pantallas: []
casos_uso: []
tags: ["actividad", "actividad_importar", "actividades", "importar"]
estado_revision: "generado"
---

# Gestionar Actividad Importar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_importar`.

## Objetivo Funcional

Gestiona ActividadImportar. Endpoint backend AJAX: importa las actividades seleccionadas y regenera su proceso cuando la app procesos esta instalada.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/actividad_importar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Endpoint backend AJAX: importa las actividades seleccionadas y regenera su proceso cuando la app `procesos` esta instalada.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
