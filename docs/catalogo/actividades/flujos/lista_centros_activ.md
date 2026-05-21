---
id: "actividades.lista_centros_activ.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Lista Centros Activ"
capacidad: "actividades.lista_centros_activ.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.lista_centros_activ"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividades/lista_centros_activ_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Lista Centros Activ

Propuesta generada automaticamente desde la capacidad `actividades.lista_centros_activ.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListaCentrosActivDatos. Endpoint backend para lista_centros_activ.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividades.pantalla.lista_centros_activ`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_ctr`
- `post.id_ctr_num`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/lista_centros_activ_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
