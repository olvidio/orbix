---
id: "pasarela.contribucion_reserva_default.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion Reserva Default"
entidades: ["ContribucionReservaDefault"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/pasarela/contribucion_reserva_default_data", "/src/pasarela/contribucion_reserva_default_guardar"]
pantallas: ["frontend/pasarela/controller/contribucion_reserva_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionReservaDefaultData", "src\\pasarela\\application\\ContribucionReservaDefaultGuardar"]
tags: ["contribucion", "contribucion_reserva_default", "data", "default", "guardar", "pasarela", "reserva"]
estado_revision: "generado"
---

# Gestionar Contribucion Reserva Default

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_reserva_default`.

## Objetivo Funcional

Gestiona ContribucionReservaDefault. Actualiza el valor por defecto del parámetro contribucion_reserva. Devuelve solo el valor por defecto del parámetro contribucion_reserva, para alimentar el formulario form_default desde el frontend.

## Acciones Detectadas

- `guardar`
- `obtener_datos`

## Endpoints

- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_reserva_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionReservaDefaultData`
- `src\pasarela\application\ContribucionReservaDefaultGuardar`

## Pistas Desde Endpoints

- Actualiza el valor por defecto del parámetro `contribucion_reserva`.
- Devuelve solo el valor por defecto del parámetro `contribucion_reserva`, para alimentar el formulario `form_default` desde el frontend.

## Errores Conocidos

- `Debe ser un numero entero del 1 al 100`
- `Falta valor por defecto`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
