---
id: "actividades.lista_sr_csv.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Lista Sr Csv"
entidades: ["ListaSrCsvListado"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_datos"]
pantallas: ["frontend/actividades/controller/lista_sr_csv.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvListado"]
tags: ["actividades", "csv", "datos", "lista", "lista_sr_csv", "sr"]
estado_revision: "generado"
---

# Gestionar Lista Sr Csv

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_sr_csv`.

## Objetivo Funcional

Gestiona ListaSrCsvListado. Endpoint backend para lista_sr_csv (listado SR + exportacion).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/lista_sr_csv_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/lista_sr_csv.php`

## Casos De Uso Detectados

- `src\actividades\application\ListaSrCsvListado`

## Pistas Desde Endpoints

- Endpoint backend para `lista_sr_csv` (listado SR + exportacion).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
