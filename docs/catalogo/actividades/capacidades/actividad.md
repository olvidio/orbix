---
id: "actividades.actividad.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad"
entidades: ["Actividad", "BorrarActividad"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
pantallas: ["frontend/actividades/controller/actividad_nuevo_curso.php"]
casos_uso: ["src\\actividades\\application\\ActividadNueva", "src\\actividades\\application\\BorrarActividad"]
tags: ["actividad", "actividades", "eliminar", "nuevo"]
estado_revision: "generado"
---

# Gestionar Actividad

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad`.

## Objetivo Funcional

Gestiona Actividad, BorrarActividad. Endpoint backend AJAX: crea una nueva actividad a partir de los datos del formulario. Endpoint backend AJAX: elimina las actividades indicadas.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_nuevo_curso.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadNueva`
- `src\actividades\application\BorrarActividad`

## Pistas Desde Endpoints

- Endpoint backend AJAX: crea una nueva actividad a partir de los datos del formulario.
- Endpoint backend AJAX: elimina las actividades indicadas.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
