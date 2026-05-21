---
id: "pasarela.contribucion_no_duerme.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Contribucion No Duerme"
entidades: ["ContribucionNoDuermeLista"]
acciones: ["listar"]
endpoints: ["/src/pasarela/contribucion_no_duerme_lista"]
pantallas: ["frontend/pasarela/controller/contribucion_no_duerme_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ContribucionNoDuermeLista"]
tags: ["contribucion", "contribucion_no_duerme", "duerme", "lista", "no", "pasarela"]
estado_revision: "generado"
---

# Gestionar Contribucion No Duerme

Propuesta generada automaticamente a partir de endpoints con prefijo comun `contribucion_no_duerme`.

## Objetivo Funcional

Gestiona ContribucionNoDuermeLista. Devuelve el listado del parámetro contribucion_no_duerme listo para serializar. Estructura: {default, excepciones: [{id_tipo_activ, etiqueta, valor}]}.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/pasarela/contribucion_no_duerme_lista`

## Pantallas Relacionadas

- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ContribucionNoDuermeLista`

## Pistas Desde Endpoints

- Devuelve el listado del parámetro `contribucion_no_duerme` listo para serializar. Estructura: `{default, excepciones: [{id_tipo_activ, etiqueta, valor}]}`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
