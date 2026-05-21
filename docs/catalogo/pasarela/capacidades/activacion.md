---
id: "pasarela.activacion.gestionar"
tipo: "capacidad"
modulo: "pasarela"
nombre: "Gestionar Activacion"
entidades: ["ActivacionLista"]
acciones: ["listar"]
endpoints: ["/src/pasarela/activacion_lista"]
pantallas: ["frontend/pasarela/controller/activacion_ajax.php"]
casos_uso: ["src\\pasarela\\application\\ActivacionLista"]
tags: ["activacion", "lista", "pasarela"]
estado_revision: "generado"
---

# Gestionar Activacion

Propuesta generada automaticamente a partir de endpoints con prefijo comun `activacion`.

## Objetivo Funcional

Gestiona ActivacionLista. Devuelve el listado del parámetro fecha_activacion listo para serializar: - default: valor por defecto. - excepciones: array de filas {id_tipo_activ, etiqueta, valor}. El frontend renderiza la tabla a partir de estos datos; este caso de uso no genera HTML.

## Acciones Detectadas

- `listar`

## Endpoints

- `/src/pasarela/activacion_lista`

## Pantallas Relacionadas

- `frontend/pasarela/controller/activacion_ajax.php`

## Casos De Uso Detectados

- `src\pasarela\application\ActivacionLista`

## Pistas Desde Endpoints

- Devuelve el listado del parámetro `fecha_activacion` listo para serializar: - `default`: valor por defecto. - `excepciones`: array de filas `{id_tipo_activ, etiqueta, valor}`. El frontend renderiza la tabla a partir de estos datos; este caso de uso no genera HTML.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
