---
id: "asistentes.tabla_peticiones.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Tabla Peticiones"
entidades: ["TablaPeticiones"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/tabla_peticiones_data"]
pantallas: ["frontend/asistentes/controller/tabla_peticiones.php"]
casos_uso: ["src\\asistentes\\application\\TablaPeticionesData"]
tags: ["asistentes", "data", "peticiones", "tabla", "tabla_peticiones"]
estado_revision: "generado"
---

# Gestionar Tabla Peticiones

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tabla_peticiones`.

## Objetivo Funcional

Gestiona TablaPeticiones. JSON para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/tabla_peticiones_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/tabla_peticiones.php`

## Casos De Uso Detectados

- `src\asistentes\application\TablaPeticionesData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
