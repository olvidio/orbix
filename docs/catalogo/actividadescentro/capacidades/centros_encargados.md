---
id: "actividadescentro.centros_encargados.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Centros Encargados"
entidades: ["CentrosEncargados"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_encargados_data"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\CentrosEncargadosData"]
tags: ["actividadescentro", "centros", "centros_encargados", "data", "encargados"]
estado_revision: "generado"
---

# Gestionar Centros Encargados

Propuesta generada automaticamente a partir de endpoints con prefijo comun `centros_encargados`.

## Objetivo Funcional

Gestiona CentrosEncargados. Devuelve los centros encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadescentro/centros_encargados_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\CentrosEncargadosData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve los centros encargados actuales de una actividad en un array serializable, junto con los flags de permiso.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
