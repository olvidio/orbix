---
id: "misas.ver_encargos_zona.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Ver Encargos Zona"
entidades: ["VerEncargosZona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
pantallas: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\VerEncargosZonaData"]
tags: ["data", "encargos", "misas", "ver", "ver_encargos_zona", "zona"]
estado_revision: "generado"
---

# Gestionar Ver Encargos Zona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ver_encargos_zona`.

## Objetivo Funcional

Gestiona VerEncargosZona. Devuelve los datos necesarios para pintar el SlickGrid de encargos de una zona + los desplegables del modal de edicion. Replica la consulta de apps/misas/controller/ver_encargos_zona.php: encargos con id_tipo_enc >= 8100 (grupo 8...) de la zona indicada, ordenados por $orden (orden, prioridad o desc_enc).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/misas/ver_encargos_zona_data`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_zona.php`

## Casos De Uso Detectados

- `src\misas\application\VerEncargosZonaData`

## Pistas Desde Endpoints

- Devuelve los datos necesarios para pintar el SlickGrid de encargos de una zona + los desplegables del modal de edicion. Replica la consulta de `apps/misas/controller/ver_encargos_zona.php`: encargos con `id_tipo_enc >= 8100` (grupo `8...`) de la zona indicada, ordenados por `$orden` (`orden`, `prioridad` o `desc_enc`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
