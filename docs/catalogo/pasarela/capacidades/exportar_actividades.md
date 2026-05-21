---
id: "pasarela.exportar_actividades.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Exportar Actividades"
entidades: ["ExportarActividades"]
acciones: ["obtener_datos"]
endpoints: ["/src/pasarela/exportar_actividades_data"]
pantallas: ["frontend/pasarela/controller/exportar_select.php"]
casos_uso: ["src\\pasarela\\application\\ExportarActividadesData"]
tags: ["actividades", "data", "exportar", "exportar_actividades", "pasarela"]
estado_revision: "generado"
---

# Gestionar Exportar Actividades

Propuesta generada automaticamente a partir de endpoints con prefijo comun `exportar_actividades`.

## Objetivo Funcional

Gestiona ExportarActividades. Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo y casas), devuelve cabeceras + filas para el listado de exportación, mezclando datos de actividades con las conversiones de pasarela. Devuelve un array serializable por {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/pasarela/exportar_actividades_data`

## Pantallas Relacionadas

- `frontend/pasarela/controller/exportar_select.php`

## Casos De Uso Detectados

- `src\pasarela\application\ExportarActividadesData`

## Pistas Desde Endpoints

- Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo y casas), devuelve cabeceras + filas para el listado de exportación, mezclando datos de actividades con las conversiones de pasarela. Devuelve un array serializable por {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
