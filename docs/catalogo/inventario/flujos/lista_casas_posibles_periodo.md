---
id: "inventario.lista_casas_posibles_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Gestionar Lista Casas Posibles Periodo"
capacidad: "inventario.lista_casas_posibles_periodo.gestionar"
pantallas_principales: []
fragmentos: ["inventario.pantalla.equipajes_casas_posibles"]
acciones: ["ejecutar"]
endpoints: ["/src/inventario/lista_casas_posibles_periodo"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Casas Posibles Periodo

Propuesta generada automaticamente desde la capacidad `inventario.lista_casas_posibles_periodo.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaCasasPosiblesPeriodo. Flujo revisado contra `src/inventario/` y `frontend/inventario/`.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `inventario.pantalla.equipajes_casas_posibles`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.inicio`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_ver_actividades_casa`

## Endpoints Del Flujo

- `/src/inventario/lista_casas_posibles_periodo`

## Errores Conocidos

No se han documentado errores en la capacidad.
