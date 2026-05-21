---
id: "asistentes.lista_ultim_que_ctr.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Ultim Que Ctr"
entidades: ["ListaUltimQueCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_ultim_que_ctr_data"]
pantallas: ["frontend/asistentes/controller/lista_ultim_que_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimQueCtrData"]
tags: ["asistentes", "ctr", "data", "lista", "lista_ultim_que_ctr", "que", "ultim"]
estado_revision: "generado"
---

# Gestionar Lista Ultim Que Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_ultim_que_ctr`.

## Objetivo Funcional

Gestiona ListaUltimQueCtr. JSON para {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_ultim_que_ctr_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_ultim_que_ctr.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaUltimQueCtrData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
