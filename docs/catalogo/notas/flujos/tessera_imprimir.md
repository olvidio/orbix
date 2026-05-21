---
id: "notas.tessera_imprimir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Tessera Imprimir"
capacidad: "notas.tessera_imprimir.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.tessera_imprimir", "notas.pantalla.tessera_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/tessera_imprimir_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Tessera Imprimir

Propuesta generada automaticamente desde la capacidad `notas.tessera_imprimir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TesseraImprimir. Datos imprimibles de tessera ya serializados (sin objetos dominio → JSON estable).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.tessera_imprimir`
- `notas.pantalla.tessera_imprimir_mpdf`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.cara`
- `post.id_nom`
- `post.id_tabla`
- `post.refresh`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/notas/tessera_imprimir_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
