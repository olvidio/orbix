---
id: "misas.desplegable_encargos.gestionar"
tipo: "capacidad"
modulo: "misas"
nombre: "Gestionar Desplegable Encargos"
entidades: ["DesplegableEncargos"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/desplegable_encargos"]
pantallas: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\DesplegableEncargosData"]
tags: ["desplegable", "desplegable_encargos", "encargos", "misas"]
estado_revision: "generado"
---

# Gestionar Desplegable Encargos

Propuesta generada automaticamente a partir de endpoints con prefijo comun `desplegable_encargos`.

## Objetivo Funcional

Gestiona DesplegableEncargos. Payload JSON para el desplegable dinamico de encargos de una zona. Sigue el contrato de desplegables de refactor.md: - id : id del <select> que montara fnjs_construir_desplegable. - opciones : map id_enc => desc_enc de los encargos con id_tipo_enc >= 8100 de la zona. - selected : id_enc preseleccionado ('' si no aplica). - blanco : true si se quiere opcion en blanco. - val_blanco: valor de la opcion en blanco. - action : handler onchange opcional (vacio por defecto).

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/misas/desplegable_encargos`

## Pantallas Relacionadas

- `frontend/misas/controller/ver_encargos_centros.php`

## Casos De Uso Detectados

- `src\misas\application\DesplegableEncargosData`

## Pistas Desde Endpoints

- Payload JSON para el desplegable dinamico de encargos de una zona. Sigue el contrato de desplegables de `refactor.md`: - `id` : id del `<select>` que montara `fnjs_construir_desplegable`. - `opciones` : map id_enc => desc_enc de los encargos con `id_tipo_enc >= 8100` de la zona. - `selected` : id_enc preseleccionado (`''` si no aplica). - `blanco` : true si se quiere opcion en blanco. - `val_blanco`: valor de la opcion en blanco. - `action` : handler `onchange` opcional (vacio por defecto).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
