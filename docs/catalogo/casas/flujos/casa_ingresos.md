---
id: "casas.casa_ingresos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Casa Ingresos"
capacidad: "casas.casa_ingresos.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.casa_ingresos_lista"]
acciones: ["listar"]
endpoints: ["/src/casas/casa_ingresos_lista_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Casa Ingresos

Propuesta generada automaticamente desde la capacidad `casas.casa_ingresos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasaIngresos. Listado económico de actividades por casa (casa_ingresos_lista).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casa_ingresos_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

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

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
