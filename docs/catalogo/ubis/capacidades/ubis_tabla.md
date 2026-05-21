---
id: "ubis.ubis_tabla.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Ubis Tabla"
entidades: ["UbisTabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_tabla_data"]
pantallas: ["frontend/ubis/controller/ubis_tabla.php"]
casos_uso: ["src\\ubis\\application\\UbisTablaData"]
tags: ["data", "tabla", "ubis", "ubis_tabla"]
estado_revision: "generado"
---

# Gestionar Ubis Tabla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ubis_tabla`.

## Objetivo Funcional

Gestiona UbisTabla. Normaliza los parámetros de entrada del request.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/ubis_tabla_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/ubis_tabla.php`

## Casos De Uso Detectados

- `src\ubis\application\UbisTablaData`

## Pistas Desde Endpoints

- Normaliza los parámetros de entrada del request.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
