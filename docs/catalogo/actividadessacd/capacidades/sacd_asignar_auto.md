---
id: "actividadessacd.sacd_asignar_auto.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacd Asignar Auto"
entidades: ["SacdAsignarAuto"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
pantallas: ["frontend/actividadessacd/controller/asignar_sacd_auto.php", "frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignarAuto"]
tags: ["actividadessacd", "asignar", "auto", "sacd", "sacd_asignar_auto"]
estado_revision: "generado"
---

# Gestionar Sacd Asignar Auto

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_asignar_auto`.

## Objetivo Funcional

Gestiona SacdAsignarAuto. Auto-asignacion masiva del sacd titular del centro encargado a actividades sr/sg sin sacd.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadessacd/sacd_asignar_auto`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/asignar_sacd_auto.php`
- `frontend/actividadessacd/view/asignar_sacd_auto.phtml`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdAsignarAuto`

## Pistas Desde Endpoints

- Endpoint backend: auto-asignacion masiva del sacd titular del centro encargado a actividades sr/sg sin sacd.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
