---
id: "asistentes.lista_est_ctr.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Est Ctr"
entidades: ["ListaEstCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_est_ctr_data"]
pantallas: ["frontend/asistentes/controller/lista_est_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaEstCtrData"]
tags: ["asistentes", "ctr", "data", "est", "lista", "lista_est_ctr"]
estado_revision: "generado"
---

# Gestionar Lista Est Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_est_ctr`.

## Objetivo Funcional

Gestiona ListaEstCtr. Listado estudios por centro (lista_est_ctr.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_est_ctr_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_est_ctr.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaEstCtrData`

## Pistas Desde Endpoints

- Listado estudios por centro (`lista_est_ctr.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
