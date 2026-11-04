---
id: "casas.casa_ingresos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar los ingresos de una casa"
capacidad: "casas.casa_ingresos.gestionar"
pantallas_principales: ["casas.pantalla.casa_ingresos_lista"]
fragmentos: ["casas.pantalla.casa_ingresos_lista"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_ingresos_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casa Ingresos

Propuesta generada automaticamente desde la capacidad `casas.casa_ingresos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver los ingresos de las actividades de una casa en un periodo.

## Punto De Entrada

Estudio económico de la casa.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_ingresos_lista`

## Escenarios Inferidos

### Consultar los ingresos de una casa

Pasos propuestos:
1. Abrir el estudio económico de la casa y elegir el periodo.
2. Se muestra la tabla de actividades con sus ingresos.

Endpoints asociados:
- `/src/casas/casa_ingresos_lista_data`

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

- `/src/casas/casa_ingresos_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** exterior > casas > Gestión económica
- **Pills2:** CASAS Y CTR > Gestión casas > Gestión económica

