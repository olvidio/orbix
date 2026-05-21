---
id: "asistentes.asistente_plaza_asignar.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Asistente Plaza Asignar"
entidades: ["AsistentePlazaAsignar"]
acciones: ["ejecutar"]
endpoints: ["/src/asistentes/asistente_plaza_asignar"]
pantallas: []
casos_uso: ["src\\asistentes\\application\\AsistentePlazaAsignar"]
tags: ["asignar", "asistente", "asistente_plaza_asignar", "asistentes", "plaza"]
estado_revision: "generado"
---

# Gestionar Asistente Plaza Asignar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `asistente_plaza_asignar`.

## Objetivo Funcional

Gestiona AsistentePlazaAsignar. Cambia la plaza de un lote de asistentes.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/asistentes/asistente_plaza_asignar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\asistentes\application\AsistentePlazaAsignar`

## Pistas Desde Endpoints

- Cambia la plaza de un lote de asistentes.

## Errores Conocidos

- `falta id_activ`
- `falta lista de seleccion`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
