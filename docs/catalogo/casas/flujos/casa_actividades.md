---
id: "casas.casa_actividades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Casa Actividades"
capacidad: "casas.casa_actividades.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_actividades_lista"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_actividades_lista_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casa Actividades

Propuesta generada automaticamente desde la capacidad `casas.casa_actividades.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasaActividades. Listado de actividades por casa y periodo (casa_actividades_lista).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_actividades_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

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

