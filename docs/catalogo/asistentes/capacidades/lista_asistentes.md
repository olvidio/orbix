---
id: "asistentes.lista_asistentes.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Asistentes"
entidades: ["ListaAsistentes"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asistentes_data"]
pantallas: ["frontend/asistentes/controller/lista_asistentes.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsistentesData"]
tags: ["asistentes", "data", "lista", "lista_asistentes"]
estado_revision: "generado"
---

# Gestionar Lista Asistentes

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_asistentes`.

## Objetivo Funcional

Gestiona ListaAsistentes. Listado de asistentes a una actividad (lista_asistentes.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_asistentes_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_asistentes.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaAsistentesData`

## Pistas Desde Endpoints

- Listado de asistentes a una actividad (`lista_asistentes.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
