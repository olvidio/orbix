---
id: "ubis.centros.gestionar"
tipo: "capacidad"
modulo: "ubis"
nombre: "Gestionar Centros"
entidades: ["Centros"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/centros_update"]
pantallas: ["frontend/ubis/controller/centros_form_labor.php", "frontend/ubis/controller/centros_form_num.php", "frontend/ubis/controller/centros_form_plazas.php", "frontend/ubis/controller/centros_que.php"]
casos_uso: ["src\\ubis\\application\\CentrosUpdate"]
tags: ["centros", "ubis", "update"]
estado_revision: "generado"
---

# Gestionar Centros

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros`.

## Objetivo Funcional

Gestiona Centros. Actualiza datos de centro DL (labor / num / plazas según POST).

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/ubis/centros_update`

## Pantallas Relacionadas

- `frontend/ubis/controller/centros_form_labor.php`
- `frontend/ubis/controller/centros_form_num.php`
- `frontend/ubis/controller/centros_form_plazas.php`
- `frontend/ubis/controller/centros_que.php`

## Casos De Uso Detectados

- `src\ubis\application\CentrosUpdate`

## Pistas Desde Endpoints

- Actualiza datos de centro DL (labor / num / plazas según POST).

## Errores Conocidos

- `Hay un error, no se ha guardado.`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
