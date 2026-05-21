---
id: "asistentes.lista_ultima_activ.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Ultima Activ"
entidades: ["ListaUltimaActiv"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultima_activ_data"]
pantallas: ["frontend/asistentes/controller/lista_ultima_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimaActivData"]
tags: ["activ", "asistentes", "data", "lista", "lista_ultima_activ", "ultima"]
estado_revision: "generado"
---

# Gestionar Lista Ultima Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_ultima_activ`.

## Objetivo Funcional

Gestiona ListaUltimaActiv. Listado última actividad / seguimiento (lista_ultima_activ.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_ultima_activ_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_ultima_activ.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaUltimaActivData`

## Pistas Desde Endpoints

- Listado última actividad / seguimiento (`lista_ultima_activ.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
