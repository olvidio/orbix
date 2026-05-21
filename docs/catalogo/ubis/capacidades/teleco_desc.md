---
id: "ubis.teleco_desc.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Teleco Desc"
entidades: ["TelecoDescLista"]
acciones: ["listar"]
endpoints: ["/src/ubis/teleco_desc_lista"]
pantallas: ["frontend/ubis/controller/teleco_desc_lista_ajax.php"]
casos_uso: ["src\\ubis\\application\\TelecoDescLista"]
tags: ["desc", "lista", "teleco", "teleco_desc", "ubis"]
estado_revision: "generado"
---

# Gestionar Teleco Desc

Propuesta generada automaticamente a partir de endpoints con prefijo comun `teleco_desc`.

## Objetivo Funcional

Gestiona TelecoDescLista. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/ubis/teleco_desc_lista`

## Pantallas Relacionadas

- `frontend/ubis/controller/teleco_desc_lista_ajax.php`

## Casos De Uso Detectados

- `src\ubis\application\TelecoDescLista`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
