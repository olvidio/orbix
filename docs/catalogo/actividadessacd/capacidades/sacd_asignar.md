---
id: "actividadessacd.sacd_asignar.gestionar"
tipo: "capacidad"
modulo: "actividadessacd"
nombre: "Gestionar Sacd Asignar"
entidades: ["SacdAsignar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_asignar"]
pantallas: ["frontend/actividadessacd/controller/activ_sacd.php", "frontend/actividadessacd/controller/asignar_sacd_auto.php", "frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
casos_uso: ["src\\actividadessacd\\application\\SacdAsignar"]
tags: ["actividadessacd", "asignar", "sacd", "sacd_asignar"]
estado_revision: "generado"
---

# Gestionar Sacd Asignar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_asignar`.

## Objetivo Funcional

Gestiona SacdAsignar. Asigna un sacd a una actividad (y, si es sv, tambien crea la asistencia).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadessacd/sacd_asignar`

## Pantallas Relacionadas

- `frontend/actividadessacd/controller/activ_sacd.php`
- `frontend/actividadessacd/controller/asignar_sacd_auto.php`
- `frontend/actividadessacd/view/asignar_sacd_auto.phtml`

## Casos De Uso Detectados

- `src\actividadessacd\application\SacdAsignar`

## Pistas Desde Endpoints

- Endpoint backend: asigna un sacd a una actividad (y, si es sv, tambien crea la asistencia).

## Errores Conocidos

- `No puede haber tantos cargos de sacd en una actividad`
- `faltan parametros id_activ / id_nom`
- `hay un error, no se ha guardado el cargo`
- `hay un error, no se ha guardado la asistencia`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
