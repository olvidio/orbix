---
id: "misas.guardar_encargo_zona.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Guardar Encargo Zona"
entidades: ["GuardarEncargoZona"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/guardar_encargo_zona"]
pantallas: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoZona"]
tags: ["encargo", "guardar", "guardar_encargo_zona", "misas", "zona"]
estado_revision: "generado"
---

# Gestionar Guardar Encargo Zona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `guardar_encargo_zona`.

## Objetivo Funcional

Gestiona GuardarEncargoZona. Inserta o actualiza un Encargo del grupo ZONAS_MISAS. - Si id_enc es 0 se crea uno nuevo con getNewId(). - Si hay valor, se carga el existente y se modifica. Devuelve un array con: - error: texto vacio si todo fue bien, mensaje del repositorio si no. - data : payload para el frontend con id_enc, lugar y el nombre del centro si se resolvio.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/guardar_encargo_zona`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_zona.php`

## Casos De Uso Detectados

- `src\misas\application\GuardarEncargoZona`

## Pistas Desde Endpoints

- Inserta o actualiza un `Encargo` del grupo `ZONAS_MISAS`. - Si `id_enc` es 0 se crea uno nuevo con `getNewId()`. - Si hay valor, se carga el existente y se modifica. Devuelve un array con: - `error`: texto vacio si todo fue bien, mensaje del repositorio si no. - `data` : payload para el frontend con `id_enc`, `lugar` y el nombre del centro si se resolvio.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
