---
id: "ubis.teleco.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Teleco"
entidades: ["Teleco"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/ubis/teleco_eliminar", "/src/ubis/teleco_guardar"]
pantallas: []
casos_uso: ["src\\ubis\\application\\TelecoEliminar", "src\\ubis\\application\\TelecoGuardar"]
tags: ["eliminar", "guardar", "teleco", "ubis"]
estado_revision: "generado"
---

# Gestionar Teleco

Propuesta generada automaticamente a partir de endpoints con prefijo comun `teleco`.

## Objetivo Funcional

Gestiona Teleco. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/ubis/teleco_eliminar`
- `/src/ubis/teleco_guardar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\ubis\application\TelecoEliminar`
- `src\ubis\application\TelecoGuardar`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
