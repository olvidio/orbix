---
id: "notas.acta_imprimir_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta Imprimir Presentacion"
capacidad: "notas.acta_imprimir_presentacion.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_imprimir", "notas.pantalla.acta_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/acta_imprimir_presentacion_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Acta Imprimir Presentacion

Propuesta generada automaticamente desde la capacidad `notas.acta_imprimir_presentacion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActaImprimirPresentacion. Datos compartidos por acta_imprimir y el HTML de acta_imprimir_mpdf.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_imprimir`
- `notas.pantalla.acta_imprimir_mpdf`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `post.acta`
- `post.cara`
- `post.refresh`
- `post.sel`

Acciones JavaScript:
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/acta_imprimir_presentacion_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
