---
id: "casas.casa_actividades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar las actividades de una casa"
capacidad: "casas.casa_actividades.gestionar"
pantallas_principales: ["casas.pantalla.casa_actividades_lista"]
fragmentos: ["casas.pantalla.casa_actividades_lista"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casa Actividades

Propuesta generada automaticamente desde la capacidad `casas.casa_actividades.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver las actividades de una casa en un periodo.

## Punto De Entrada

Estudio económico de la casa.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_actividades_lista`

## Escenarios Inferidos

### Consultar las actividades de una casa

Pasos propuestos:
1. Abrir el estudio económico de la casa y elegir el periodo.
2. Aparece la lista de actividades; pulsar una para abrir su ficha.

Endpoints asociados:
- `/src/casas/casa_actividades_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/casas/casa_actividades_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** exterior > casas > lista actividades
- **Pills2:** exterior > casas > lista actividades

