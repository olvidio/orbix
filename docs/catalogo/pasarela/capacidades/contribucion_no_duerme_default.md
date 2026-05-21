---
id: "pasarela.contribucion_no_duerme_default.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion No Duerme Default"
entidades: ["ContribucionNoDuermeDefault"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/pasarela/contribucion_no_duerme_default_data", "/src/pasarela/contribucion_no_duerme_default_guardar"]
pantallas: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeDefaultData", "src\\pasarela\\application\\ContribucionNoDuermeDefaultGuardar"]
tags: ["contribucion", "contribucion_no_duerme_default", "data", "default", "duerme", "guardar", "no", "pasarela"]
estado_revision: "generado"
---

# Gestionar Contribucion No Duerme Default

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_no_duerme_default`.

## Objetivo Funcional

Gestiona ContribucionNoDuermeDefault. Actualiza el valor por defecto del parámetro contribucion_no_duerme. Devuelve solo el valor por defecto del parámetro contribucion_no_duerme, para alimentar el formulario form_default desde el frontend.

## Acciones Detectadas

- `guardar`
- `obtener_datos`

## Endpoints

- `/src/pasarela/contribucion_no_duerme_default_data`
- `/src/pasarela/contribucion_no_duerme_default_guardar`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionNoDuermeDefaultData`
- `src\pasarela\application\ContribucionNoDuermeDefaultGuardar`

## Pistas Desde Endpoints

- Actualiza el valor por defecto del parámetro `contribucion_no_duerme`.
- Devuelve solo el valor por defecto del parámetro `contribucion_no_duerme`, para alimentar el formulario `form_default` desde el frontend.

## Errores Conocidos

- `Debe ser un numero entero del 1 al 100`
- `Falta valor por defecto`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
