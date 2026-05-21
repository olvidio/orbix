---
id: "cartaspresentacion.cartas_presentacion_buscar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Cartas Presentacion Buscar"
capacidad: "cartaspresentacion.cartas_presentacion_buscar.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_buscar"]
acciones: ["obtener_datos"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_buscar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cartas Presentacion Buscar

Propuesta generada automaticamente desde la capacidad `cartaspresentacion.cartas_presentacion_buscar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CartasPresentacionBuscarOpciones. Opciones del formulario de busqueda de cartas de presentacion (region, pais, delegacion).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cartaspresentacion.pantalla.cartas_presentacion_buscar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.btn_ok`
- `html.poblacion`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`

## Endpoints Del Flujo

- `/src/cartaspresentacion/cartas_presentacion_buscar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
