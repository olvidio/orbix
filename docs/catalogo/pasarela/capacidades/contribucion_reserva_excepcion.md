---
id: "pasarela.contribucion_reserva_excepcion.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion Reserva Excepcion"
entidades: ["ContribucionReservaExcepcion"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/contribucion_reserva_excepcion_eliminar", "/src/pasarela/contribucion_reserva_excepcion_guardar"]
pantallas: ["frontend/pasarela/controller/contribucion_reserva_ajax.php", "frontend/pasarela/controller/contribucion_reserva_lista.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionReservaExcepcionEliminar", "src\\pasarela\\application\\ContribucionReservaExcepcionGuardar"]
tags: ["contribucion", "contribucion_reserva_excepcion", "eliminar", "excepcion", "guardar", "pasarela", "reserva"]
estado_revision: "generado"
---

# Gestionar Contribucion Reserva Excepcion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_reserva_excepcion`.

## Objetivo Funcional

Gestiona ContribucionReservaExcepcion. Elimina una excepción del parámetro contribucion_reserva para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro contribucion_reserva para un id_tipo_activ concreto.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/pasarela/contribucion_reserva_excepcion_eliminar`
- `/src/pasarela/contribucion_reserva_excepcion_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`
- `frontend/pasarela/controller/contribucion_reserva_lista.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionReservaExcepcionEliminar`
- `src\pasarela\application\ContribucionReservaExcepcionGuardar`

## Pistas Desde Endpoints

- Elimina una excepción del parámetro `contribucion_reserva` para un `id_tipo_activ` concreto.
- Inserta o actualiza una excepción del parámetro `contribucion_reserva` para un `id_tipo_activ` concreto.

## Errores Conocidos

- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
