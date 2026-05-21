---
id: "actividadescentro.centro_encargado_asignar.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Centro Encargado Asignar"
entidades: ["CentroEncargadoAsignar"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadescentro/centro_encargado_asignar"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\CentroEncargadoAsignar"]
tags: ["actividadescentro", "asignar", "centro", "centro_encargado_asignar", "encargado"]
estado_revision: "generado"
---

# Gestionar Centro Encargado Asignar

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centro_encargado_asignar`.

## Objetivo Funcional

Gestiona CentroEncargadoAsignar. Asigna un CentroEncargado a una actividad.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/actividadescentro/centro_encargado_asignar`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\CentroEncargadoAsignar`

## Pistas Desde Endpoints

- Endpoint backend: asigna un CentroEncargado a una actividad.

## Errores Conocidos

- `faltan parametros id_activ / id_ubi`
- `hay un error, no se ha guardado el centro encargado`

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
