---
id: "actividadestudios.matriculas_pendientes.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar Matriculas Pendientes"
entidades: ["MatriculasPendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
pantallas: ["frontend/actividadestudios/controller/matriculas_pendientes.php"]
casos_uso: ["src\\actividadestudios\\application\\MatriculasPendientesData"]
tags: ["actividadestudios", "data", "matriculas", "matriculas_pendientes", "pendientes"]
estado_revision: "generado"
---

# Gestionar Matriculas Pendientes

Propuesta generada automaticamente a partir de endpoints con prefijo comun `matriculas_pendientes`.

## Objetivo Funcional

Gestiona MatriculasPendientes. Filas para frontend/actividadestudios/controller/matriculas_pendientes.php.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadestudios/matriculas_pendientes_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/matriculas_pendientes.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\MatriculasPendientesData`

## Pistas Desde Endpoints

- Filas para `frontend/actividadestudios/controller/matriculas_pendientes.php`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
