---
id: "pasarela.contribucion_reserva.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion Reserva"
entidades: ["ContribucionReservaLista"]
acciones: ["listar"]
endpoints: ["/src/pasarela/contribucion_reserva_lista"]
pantallas: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionReservaLista"]
tags: ["contribucion", "contribucion_reserva", "lista", "pasarela", "reserva"]
estado_revision: "generado"
---

# Gestionar Contribucion Reserva

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_reserva`.

## Objetivo Funcional

Gestiona ContribucionReservaLista. Devuelve el listado del parámetro contribucion_reserva listo para serializar. Estructura: {default, excepciones: [{id_tipo_activ, etiqueta, valor}]}.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/pasarela/contribucion_reserva_lista`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionReservaLista`

## Pistas Desde Endpoints

- Devuelve el listado del parámetro `contribucion_reserva` listo para serializar. Estructura: `{default, excepciones: [{id_tipo_activ, etiqueta, valor}]}`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
