---
id: "actividades.actividad_select_ubi_desplegable.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Select Ubi Desplegable"
entidades: ["ActividadSelectUbi"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable"]
pantallas: ["frontend/actividades/controller/actividad_select_ubi.php", "frontend/actividades/view/actividad_select_ubi.phtml"]
casos_uso: ["src\\actividades\\application\\ActividadSelectUbiData"]
tags: ["actividad", "actividad_select_ubi_desplegable", "actividades", "desplegable", "select", "ubi"]
estado_revision: "generado"
---

# Gestionar Actividad Select Ubi Desplegable

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_select_ubi_desplegable`.

## Objetivo Funcional

Gestiona ActividadSelectUbi. Endpoint backend que devuelve las opciones (value => label) de los desplegables de la pantalla "seleccionar lugar para una actividad".

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividades/actividad_select_ubi_desplegable`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_select_ubi.php`
- `frontend/actividades/view/actividad_select_ubi.phtml`

## Casos De Uso Detectados

- `src\actividades\application\ActividadSelectUbiData`

## Pistas Desde Endpoints

- Endpoint backend que devuelve las opciones (value => label) de los desplegables de la pantalla "seleccionar lugar para una actividad".

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
