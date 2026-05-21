---
id: "actividadescentro.lista_actividades_ctr.gestionar"
tipo: "capacidad"
modulo: "actividadescentro"
nombre: "Gestionar Lista Actividades Ctr"
entidades: ["ListaActividadesCtr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/lista_actividades_ctr_data"]
pantallas: []
casos_uso: ["src\\actividadescentro\\application\\ListaActividadesCtrData"]
tags: ["actividades", "actividadescentro", "ctr", "data", "lista", "lista_actividades_ctr"]
estado_revision: "generado"
---

# Gestionar Lista Actividades Ctr

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_actividades_ctr`.

## Objetivo Funcional

Gestiona ListaActividadesCtr. Devuelve el listado de actividades del tipo + periodo elegidos, junto con los centros encargados de cada una y los flags de permiso (ver / modificar / crear) para cada fila.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadescentro/lista_actividades_ctr_data`

## Pantallas Relacionadas

No se han detectado pantallas relacionadas.

## Casos De Uso Detectados

- `src\actividadescentro\application\ListaActividadesCtrData`

## Pistas Desde Endpoints

- Endpoint backend: devuelve el listado de actividades del tipo + periodo elegidos, junto con los centros encargados de cada una y los flags de permiso (ver / modificar / crear) para cada fila.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
