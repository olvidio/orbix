---
id: "actividades.lista_sr_csv_que.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Lista Sr Csv Que"
entidades: ["ListaSrCsvQueDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_sr_csv_que_datos"]
pantallas: ["frontend/actividades/controller/lista_sr_csv_que.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvQueDatos"]
tags: ["actividades", "csv", "datos", "lista", "lista_sr_csv_que", "que", "sr"]
estado_revision: "generado"
---

# Gestionar Lista Sr Csv Que

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_sr_csv_que`.

## Objetivo Funcional

Gestiona ListaSrCsvQueDatos. Endpoint backend para lista_sr_csv_que.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/lista_sr_csv_que_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/lista_sr_csv_que.php`

## Casos De Uso Detectados

- `src\actividades\application\ListaSrCsvQueDatos`

## Pistas Desde Endpoints

- Endpoint backend para `lista_sr_csv_que`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
