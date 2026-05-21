---
id: "actividades.actividad_status_labels.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Status Labels"
entidades: ["ActividadStatusLabelsDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_status_labels_datos"]
pantallas: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadStatusLabelsDatos"]
tags: ["actividad", "actividad_status_labels", "actividades", "datos", "labels", "status"]
estado_revision: "generado"
---

# Gestionar Actividad Status Labels

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_status_labels`.

## Objetivo Funcional

Gestiona ActividadStatusLabelsDatos. Etiquetas de status ({.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_status_labels_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadStatusLabelsDatos`

## Pistas Desde Endpoints

- Etiquetas de status ({

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
