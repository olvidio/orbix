---
id: "pasarela.contribucion_no_duerme_excepcion.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion No Duerme Excepcion"
entidades: ["ContribucionNoDuermeExcepcion"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/contribucion_no_duerme_excepcion_eliminar", "/src/pasarela/contribucion_no_duerme_excepcion_guardar"]
pantallas: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php", "frontend/pasarela/controller/contribucion_no_duerme_lista.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeExcepcionEliminar", "src\\pasarela\\application\\ContribucionNoDuermeExcepcionGuardar"]
tags: ["contribucion", "contribucion_no_duerme_excepcion", "duerme", "eliminar", "excepcion", "guardar", "no", "pasarela"]
estado_revision: "generado"
---

# Gestionar Contribucion No Duerme Excepcion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_no_duerme_excepcion`.

## Objetivo Funcional

Gestiona ContribucionNoDuermeExcepcion. Elimina una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto.

## Acciones Detectadas

- `eliminar`
- `guardar`

## Endpoints

- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`
- `frontend/pasarela/controller/contribucion_no_duerme_lista.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionNoDuermeExcepcionEliminar`
- `src\pasarela\application\ContribucionNoDuermeExcepcionGuardar`

## Pistas Desde Endpoints

- Elimina una excepción del parámetro `contribucion_no_duerme` para un `id_tipo_activ` concreto.
- Inserta o actualiza una excepción del parámetro `contribucion_no_duerme` para un `id_tipo_activ` concreto.

## Errores Conocidos

- `Debe ser un numero entero del 1 al 100`
- `Falta id_tipo_activ`
- `Falta valor de contribución`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
