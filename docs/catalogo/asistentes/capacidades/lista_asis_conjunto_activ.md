---
id: "asistentes.lista_asis_conjunto_activ.gestionar"
tipo: "capacidad"
modulo: "asistentes"
nombre: "Gestionar Lista Asis Conjunto Activ"
entidades: ["ListaAsisConjuntoActiv"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/lista_asis_conjunto_activ_data"]
pantallas: ["frontend/asistentes/controller/lista_asis_conjunto_activ.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsisConjuntoActivData"]
tags: ["activ", "asis", "asistentes", "conjunto", "data", "lista", "lista_asis_conjunto_activ"]
estado_revision: "generado"
---

# Gestionar Lista Asis Conjunto Activ

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_asis_conjunto_activ`.

## Objetivo Funcional

Gestiona ListaAsisConjuntoActiv. Listados conjuntos de plazas/actividades (lista_asis_conjunto_activ.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/asistentes/lista_asis_conjunto_activ_data`

## Pantallas Relacionadas

- `frontend/asistentes/controller/lista_asis_conjunto_activ.php`

## Casos De Uso Detectados

- `src\asistentes\application\ListaAsisConjuntoActivData`

## Pistas Desde Endpoints

- Listados conjuntos de plazas/actividades (`lista_asis_conjunto_activ.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
