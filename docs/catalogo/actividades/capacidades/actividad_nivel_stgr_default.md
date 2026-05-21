---
id: "actividades.actividad_nivel_stgr_default.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Actividad Nivel Stgr Default"
entidades: ["ActividadVerDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/actividad_nivel_stgr_default_datos"]
pantallas: ["frontend/actividades/controller/actividad_ver.php"]
casos_uso: ["src\\actividades\\application\\ActividadVerDatos"]
tags: ["actividad", "actividad_nivel_stgr_default", "actividades", "datos", "default", "nivel", "stgr"]
estado_revision: "generado"
---

# Gestionar Actividad Nivel Stgr Default

Propuesta generada automaticamente a partir de endpoints con prefijo comun `actividad_nivel_stgr_default`.

## Objetivo Funcional

Gestiona ActividadVerDatos. Nivel STGR por defecto según id_tipo_activ ({.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/actividad_nivel_stgr_default_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/actividad_ver.php`

## Casos De Uso Detectados

- `src\actividades\application\ActividadVerDatos`

## Pistas Desde Endpoints

- Nivel STGR por defecto según id_tipo_activ ({

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
