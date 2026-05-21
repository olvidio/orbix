---
id: "actividades.lista_activ.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Lista Activ"
entidades: ["ListaActivTabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_activ_datos"]
pantallas: ["frontend/actividades/controller/lista_activ.php", "frontend/actividades/controller/lista_activ_que.php"]
casos_uso: ["src\\actividades\\application\\ListaActivTabla"]
tags: ["activ", "actividades", "datos", "lista", "lista_activ"]
estado_revision: "generado"
---

# Gestionar Lista Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_activ`.

## Objetivo Funcional

Gestiona ListaActivTabla. JSON del listado lista_activ: filtros POST → {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/lista_activ_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/lista_activ.php`
- `frontend/actividades/controller/lista_activ_que.php`

## Casos De Uso Detectados

- `src\actividades\application\ListaActivTabla`

## Pistas Desde Endpoints

- JSON del listado `lista_activ`: filtros POST → {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
