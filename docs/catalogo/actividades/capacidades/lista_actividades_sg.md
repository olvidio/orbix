---
id: "actividades.lista_actividades_sg.gestionar"
tipo: "capacidad"
modulo: "actividades"
nombre: "Gestionar Lista Actividades Sg"
entidades: ["ListaActividadesSgListado"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_actividades_sg_datos"]
pantallas: ["frontend/actividades/controller/lista_actividades_sg.php"]
casos_uso: ["src\\actividades\\application\\ListaActividadesSgListado"]
tags: ["actividades", "datos", "lista", "lista_actividades_sg", "sg"]
estado_revision: "generado"
---

# Gestionar Lista Actividades Sg

Propuesta generada automaticamente a partir de endpoints con prefijo comun `lista_actividades_sg`.

## Objetivo Funcional

Gestiona ListaActividadesSgListado. JSON del listado para lista_actividades_sg: POST → {.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividades/lista_actividades_sg_datos`

## Pantallas Relacionadas

- `frontend/actividades/controller/lista_actividades_sg.php`

## Casos De Uso Detectados

- `src\actividades\application\ListaActividadesSgListado`

## Pistas Desde Endpoints

- JSON del listado para `lista_actividades_sg`: POST → {

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
