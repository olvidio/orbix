---
id: "misas.modificar_plantilla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Modificar Plantilla"
capacidad: "misas.modificar_plantilla.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_plantilla"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/modificar_plantilla_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Modificar Plantilla

Propuesta generada automaticamente desde la capacidad `misas.modificar_plantilla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlanDeMisasPantalla. Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_plantilla`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_zona`
- `form.importar_de_plantilla`
- `form.orden`
- `form.tipo_plantilla`
- `form.tipo_plantilla_destino`
- `form.tipo_plantilla_origen`
- `html.importar`

Acciones JavaScript:
- `button:importar`
- `fnjs_importar_de_plantilla_zona`
- `fnjs_ver_plantilla_zona`

## Endpoints Del Flujo

- `/src/misas/modificar_plantilla_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
