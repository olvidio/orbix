---
id: "asistentes.lista_activ_ctr.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Activ Ctr"
entidades: ["ListaActivCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_activ_ctr_data"]
pantallas: ["frontend/asistentes/controller/lista_activ_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaActivCtrData"]
tags: ["activ", "asistentes", "ctr", "data", "lista", "lista_activ_ctr"]
estado_revision: "generado"
---

# Gestionar Lista Activ Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_activ_ctr`.

## Objetivo Funcional

Gestiona ListaActivCtr. Asistentes a actividades por centro (lista_activ_ctr.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_activ_ctr_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_activ_ctr.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaActivCtrData`

## Pistas Desde Endpoints

- Asistentes a actividades por centro (`lista_activ_ctr.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
