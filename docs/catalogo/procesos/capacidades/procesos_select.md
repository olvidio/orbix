---
id: "procesos.procesos_select.gestionar"
tipo: "capacidad"
modulo: "procesos"
nombre: "Gestionar Procesos Select"
entidades: ["ProcesosSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/procesos/procesos_select_data"]
pantallas: ["frontend/procesos/controller/procesos_select.php"]
casos_uso: ["src\\procesos\\application\\ProcesosSelectData"]
tags: ["data", "procesos", "procesos_select", "select"]
estado_revision: "generado"
---

# Gestionar Procesos Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `procesos_select`.

## Objetivo Funcional

Gestiona ProcesosSelect. Caso de uso: datos para la pantalla procesos_select. Devuelve las opciones del desplegable de tipo de proceso para que la vista frontend monte el frontend\shared\web\Desplegable y los web\Hash correspondientes.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/procesos/procesos_select_data`

## Pantallas Relacionadas

- `frontend/procesos/controller/procesos_select.php`

## Casos De Uso Detectados

- `src\procesos\application\ProcesosSelectData`

## Pistas Desde Endpoints

- Caso de uso: datos para la pantalla `procesos_select`. Devuelve las opciones del desplegable de tipo de proceso para que la vista frontend monte el `frontend\shared\web\Desplegable` y los `web\Hash` correspondientes.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
