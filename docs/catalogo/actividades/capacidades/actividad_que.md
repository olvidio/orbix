---
id: "actividades.actividad_que.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Que"
entidades: ["ActividadQueDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_que_datos"]
pantallas: ["frontend/actividades/controller/actividad_que.php", "frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php", "frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php"]
casos_uso: ["src\\actividades\\application\\ActividadQueDatos"]
tags: ["actividad", "actividad_que", "actividades", "datos", "que"]
estado_revision: "generado"
---

# Gestionar Actividad Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_que`.

## Objetivo Funcional

Gestiona ActividadQueDatos. HTML del bloque tipo de actividad (desplegables) para actividad_que.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_que_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_que.php`
- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`
- `frontend/actividadtarifas/controller/tarifa_tipo_actividad_form.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadQueDatos`

## Pistas Desde Endpoints

- Endpoint backend: HTML del bloque tipo de actividad (desplegables) para actividad_que.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
