---
id: "notas.acta.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Acta"
entidades: ["Acta"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_nueva"]
pantallas: ["frontend/notas/controller/acta_select.php", "frontend/notas/controller/acta_ver.php"]
casos_uso: ["src\\notas\\application\\ActaEliminar", "src\\notas\\application\\ActaNueva"]
tags: ["acta", "eliminar", "notas", "nueva"]
estado_revision: "generado"
---

# Gestionar Acta

Propuesta generada automaticamente a partir de endpoints con prefijo comun `acta`.

## Objetivo Funcional

Gestiona Acta. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `crear`
- `eliminar`

## Endpoints

- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

## Pantallas Relacionadas

- `frontend/notas/controller/acta_select.php`
- `frontend/notas/controller/acta_ver.php`

## Casos De Uso Detectados

- `src\notas\application\ActaEliminar`
- `src\notas\application\ActaNueva`

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

- `No se encuentra el acta`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
