---
id: "misas.desplegable_sacd.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Desplegable Sacd"
entidades: ["DesplegableSacd"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_sacd"]
pantallas: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\DesplegableSacdData"]
tags: ["desplegable", "desplegable_sacd", "misas", "sacd"]
estado_revision: "generado"
---

# Gestionar Desplegable Sacd

Propuesta generada automaticamente a partir de endpoints con prefijo comun `desplegable_sacd`.

## Objetivo Funcional

Gestiona DesplegableSacd. Opciones del desplegable dinámico de SACD en el modal de la cuadrícula de zona. El payload sigue el espíritu del contrato de refactor.md (id, selected, filas ordenadas). rows conserva el orden del HTML legacy: opción actual, opción en blanco si aplica, resto ordenado por clave.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/desplegable_sacd`

## Pantallas Relacionadas

- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Casos De Uso Detectados

- `src\misas\application\DesplegableSacdData`

## Pistas Desde Endpoints

- Opciones del desplegable dinámico de SACD en el modal de la cuadrícula de zona. El payload sigue el espíritu del contrato de `refactor.md` (id, selected, filas ordenadas). `rows` conserva el orden del HTML legacy: opción actual, opción en blanco si aplica, resto ordenado por clave.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
