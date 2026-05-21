---
id: "menus.menus_generar_txt.gestionar"
tipo: "capacidad"
modulo: "menus"
nombre: "Gestionar Menus Generar Txt"
entidades: ["MenusGenerarTxt"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/menus_generar_txt"]
pantallas: []
casos_uso: []
tags: ["generar", "menus", "menus_generar_txt", "txt"]
estado_revision: "generado"
---

# Gestionar Menus Generar Txt

Propuesta generada automaticamente a partir de endpoints con prefijo comun `menus_generar_txt`.

## Objetivo Funcional

Gestiona MenusGenerarTxt. Esta página genera un fichero con todos los textos de los menús que hay en la base de datos, para poder traducirlos por gettex.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/menus/menus_generar_txt`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Esta página genera un fichero con todos los textos de los menús que hay en la base de datos, para poder traducirlos por gettex

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
