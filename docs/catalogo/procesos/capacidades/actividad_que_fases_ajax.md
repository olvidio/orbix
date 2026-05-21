---
id: "procesos.actividad_que_fases_ajax.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Actividad Que Fases Ajax"
entidades: ["ActividadQueFasesCuadro"]
acciones: ["ejecutar"]
endpoints: ["/src/procesos/actividad_que_fases_ajax"]
pantallas: ["frontend/actividades/controller/actividad_que.php"]
casos_uso: ["src\\procesos\\application\\ActividadQueFasesCuadro"]
tags: ["actividad", "actividad_que_fases_ajax", "ajax", "fases", "procesos", "que"]
estado_revision: "generado"
---

# Gestionar Actividad Que Fases Ajax

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_que_fases_ajax`.

## Objetivo Funcional

Gestiona ActividadQueFasesCuadro. Caso de uso: devuelve la lista de fases aplicables al tipo de actividad indicado (estructura pura) para construir los checkboxes de fases_on o fases_off del filtro de busqueda de actividades.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/procesos/actividad_que_fases_ajax`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_que.php`

## Casos De Uso Detectados

- `src\procesos\application\ActividadQueFasesCuadro`

## Pistas Desde Endpoints

- Caso de uso: devuelve la lista de fases aplicables al tipo de actividad indicado (estructura pura) para construir los checkboxes de `fases_on` o `fases_off` del filtro de busqueda de actividades.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
