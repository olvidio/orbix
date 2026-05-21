---
id: "actividades.lista_centros_activ.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Lista Centros Activ"
entidades: ["ListaCentrosActivDatos"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
pantallas: ["frontend/actividades/controller/lista_centros_activ.php"]
casos_uso: ["src\\actividades\\application\\ListaCentrosActivDatos"]
tags: ["activ", "actividades", "centros", "datos", "lista", "lista_centros_activ"]
estado_revision: "generado"
---

# Gestionar Lista Centros Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_centros_activ`.

## Objetivo Funcional

Gestiona ListaCentrosActivDatos. Endpoint backend para lista_centros_activ.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/lista_centros_activ_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/lista_centros_activ.php`

## Casos De Uso Detectados

- `src\actividades\application\ListaCentrosActivDatos`

## Pistas Desde Endpoints

- Endpoint backend para `lista_centros_activ`.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
