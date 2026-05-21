---
id: "actividadestudios.profesores_desplegable.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Profesores Desplegable"
entidades: ["ProfesoresDesplegable"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/profesores_desplegable_data"]
pantallas: ["frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php"]
casos_uso: ["src\\actividadestudios\\application\\ProfesoresDesplegableData"]
tags: ["actividadestudios", "data", "desplegable", "profesores", "profesores_desplegable"]
estado_revision: "generado"
---

# Gestionar Profesores Desplegable

Propuesta generada automaticamente a partir de endpoints con prefijo comun `profesores_desplegable`.

## Objetivo Funcional

Gestiona ProfesoresDesplegable. Devuelve JSON con los datos para construir el desplegable de profesores.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadestudios/profesores_desplegable_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/form_asignaturas_de_una_actividad.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\ProfesoresDesplegableData`

## Pistas Desde Endpoints

- Devuelve JSON con los datos para construir el desplegable de profesores.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
