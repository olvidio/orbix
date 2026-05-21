---
id: "casas.ingreso_plazas_previstas.gestionar"
tipo: "capacidad"
modulo: "casas"
nombre: "Gestionar Ingreso Plazas Previstas"
entidades: ["IngresoPlazasPrevistas"]
acciones: ["crear_actualizar"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update"]
pantallas: ["frontend/casas/controller/prevision_asistentes.php"]
casos_uso: ["src\\casas\\application\\IngresoPlazasPrevistasUpdate"]
tags: ["casas", "ingreso", "ingreso_plazas_previstas", "plazas", "previstas", "update"]
estado_revision: "generado"
---

# Gestionar Ingreso Plazas Previstas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ingreso_plazas_previstas`.

## Objetivo Funcional

Gestiona IngresoPlazasPrevistas. Actualiza num_asistentes_previstos de un Ingreso desde la TablaEditable de prevision_asistentes.

## Acciones Detectadas

- `crear_actualizar`

## Endpoints

- `/src/casas/ingreso_plazas_previstas_update`

## Pantallas Relacionadas

- `frontend/casas/controller/prevision_asistentes.php`

## Casos De Uso Detectados

- `src\casas\application\IngresoPlazasPrevistasUpdate`

## Pistas Desde Endpoints

- Endpoint backend: actualiza `num_asistentes_previstos` de un `Ingreso` desde la `TablaEditable` de `prevision_asistentes`.

## Errores Conocidos

- `Hay un error, no se ha guardado`
- `no se encuentra el ingreso`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
