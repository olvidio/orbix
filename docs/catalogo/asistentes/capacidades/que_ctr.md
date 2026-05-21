---
id: "asistentes.que_ctr.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Que Ctr"
entidades: ["QueCtr"]
acciones: ["listar"]
endpoints: ["/src/asistentes/que_ctr_lista_data"]
pantallas: ["frontend/asistentes/controller/que_ctr_lista.php"]
casos_uso: ["src\\asistentes\\application\\QueCtrListaData"]
tags: ["asistentes", "ctr", "data", "lista", "que", "que_ctr"]
estado_revision: "generado"
---

# Gestionar Que Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `que_ctr`.

## Objetivo Funcional

Gestiona QueCtr. JSON para {.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/asistentes/que_ctr_lista_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/que_ctr_lista.php`

## Casos De Uso Detectados

- `src\asistentes\application\QueCtrListaData`

## Pistas Desde Endpoints

- JSON para {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
