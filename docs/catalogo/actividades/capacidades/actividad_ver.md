---
id: "actividades.actividad_ver.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Ver"
entidades: ["ActividadVerDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_ver_datos"]
pantallas: ["frontend/actividades/controller/actividad_ver.php", "frontend/actividades/controller/planning_casa_modificar.php", "frontend/actividades/controller/planning_casa_nueva.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividad", "actividad_ver", "actividades", "datos", "ver"]
estado_revision: "generado"
---

# Gestionar Actividad Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_ver`.

## Objetivo Funcional

Gestiona ActividadVerDatos. Devuelve los fragmentos HTML y valores auxiliares que necesita el formulario "ver/editar actividad" para renderizarse sin que el frontend acceda directamente a src/.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_ver_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_ver.php`
- `frontend/actividades/controller/planning_casa_modificar.php`
- `frontend/actividades/controller/planning_casa_nueva.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadVerDatos`

## Pistas Desde Endpoints

- Endpoint backend: devuelve los fragmentos HTML y valores auxiliares que necesita el formulario "ver/editar actividad" para renderizarse sin que el frontend acceda directamente a `src/`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
