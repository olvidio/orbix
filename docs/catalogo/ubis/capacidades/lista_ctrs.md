---
id: "ubis.lista_ctrs.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Lista Ctrs"
entidades: ["CentrosS"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/lista_ctrs_data"]
pantallas: ["frontend/ubis/controller/lista_ctrs.php"]
casos_uso: ["src\\ubis\\application\\CentrosSListaData"]
tags: ["ctrs", "data", "lista", "lista_ctrs", "ubis"]
estado_revision: "generado"
---

# Gestionar Lista Ctrs

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_ctrs`.

## Objetivo Funcional

Gestiona CentrosS. Listado de centros de tipo 's' (sacerdotes) con el número de personas s asignadas en cada uno, y el total global.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/ubis/lista_ctrs_data`

## Pantallas Relacionadas

- `frontend/ubis/controller/lista_ctrs.php`

## Casos De Uso Detectados

- `src\ubis\application\CentrosSListaData`

## Pistas Desde Endpoints

- Listado de centros de tipo 's' (sacerdotes) con el número de personas s asignadas en cada uno, y el total global.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
