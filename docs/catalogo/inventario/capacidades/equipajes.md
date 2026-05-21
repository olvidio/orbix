---
id: "inventario.equipajes.gestionar"
tipo: "capacidad"
modulo: "inventario"
nombre: "Gestionar Equipajes"
entidades: ["Equipaje"]
acciones: ["eliminar"]
endpoints: ["/src/inventario/equipajes_eliminar"]
pantallas: []
casos_uso: ["src\\inventario\\application\\EquipajeEliminar"]
tags: ["eliminar", "equipajes", "inventario"]
estado_revision: "generado"
---

# Gestionar Equipajes

Propuesta generada automaticamente a partir de endpoints con prefijo comun `equipajes`.

## Objetivo Funcional

Gestiona Equipaje. Borrado de un equipaje (antes solo en equipajes_eliminar.php).

## Acciones Detectadas

- `eliminar`

## Endpoints

- `/src/inventario/equipajes_eliminar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\inventario\application\EquipajeEliminar`

## Pistas Desde Endpoints

- Borrado de un equipaje (antes solo en `equipajes_eliminar.php`).

## Errores Conocidos

- `falta id_equipaje`
- `hay un error, no se ha eliminado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
