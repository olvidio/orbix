---
id: "actividades.actividad_fases_completadas.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Fases Completadas"
entidades: ["ActividadFasesCompletadasDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_fases_completadas_datos"]
pantallas: ["frontend/actividades/helpers/PrefillPermActividadesFases.php"]
casos_uso: ["src\\actividades\\application\\ActividadFasesCompletadasDatos"]
tags: ["actividad", "actividad_fases_completadas", "actividades", "completadas", "datos", "fases"]
estado_revision: "generado"
---

# Gestionar Actividad Fases Completadas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_fases_completadas`.

## Objetivo Funcional

Gestiona ActividadFasesCompletadasDatos. JSON: lista de fases completadas para id_activ (alimentar setFasesCompletadas en sesión).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_fases_completadas_datos`

## Pantallas Relacionadas

- `frontend/actividades/helpers/PrefillPermActividadesFases.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadFasesCompletadasDatos`

## Pistas Desde Endpoints

- JSON: lista de fases completadas para id_activ (alimentar setFasesCompletadas en sesión).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
