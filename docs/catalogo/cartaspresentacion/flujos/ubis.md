---
id: "cartaspresentacion.ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Ubis"
capacidad: "cartaspresentacion.ubis.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_ubis_lista"]
acciones: ["listar"]
endpoints: ["/src/cartaspresentacion/ubis_lista_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ubis

Propuesta generada automaticamente desde la capacidad `cartaspresentacion.ubis.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CartasPresentacionUbis. Listado de centros con el estado de su carta de presentacion, en dos variantes (delegacion del usuario o centros extranjeros).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cartaspresentacion.pantalla.cartas_presentacion_ubis_lista`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/cartaspresentacion/ubis_lista_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.poblacion_sel`
- `post.tipo_lista`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/cartaspresentacion/ubis_lista_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
