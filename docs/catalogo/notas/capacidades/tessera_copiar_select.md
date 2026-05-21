---
id: "notas.tessera_copiar_select.gestionar"
tipo: "capacidad"
modulo: "notas"
nombre: "Gestionar Tessera Copiar Select"
entidades: ["TesseraCopiarSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_copiar_select_data"]
pantallas: ["frontend/notas/controller/tessera_copiar_select.php"]
casos_uso: ["src\\notas\\application\\TesseraCopiarSelectData"]
tags: ["copiar", "data", "notas", "select", "tessera", "tessera_copiar_select"]
estado_revision: "generado"
---

# Gestionar Tessera Copiar Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `tessera_copiar_select`.

## Objetivo Funcional

Gestiona TesseraCopiarSelect. Prepara los datos para elegir a que persona (con el mismo primer apellido) se copiara la tessera de otra persona. Devuelve ['nom' => string, 'posibles_personas' => [id_nom => nombre]]. Lanza RuntimeException si no encuentra la persona origen ni como numerario ni como agregado.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/notas/tessera_copiar_select_data`

## Pantallas Relacionadas

- `frontend/notas/controller/tessera_copiar_select.php`

## Casos De Uso Detectados

- `src\notas\application\TesseraCopiarSelectData`

## Pistas Desde Endpoints

- Prepara los datos para elegir a que persona (con el mismo primer apellido) se copiara la tessera de otra persona. Devuelve `['nom' => string, 'posibles_personas' => [id_nom => nombre]]`. Lanza `RuntimeException` si no encuentra la persona origen ni como numerario ni como agregado.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
