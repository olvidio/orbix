---
id: "pasarela.exportar_actividades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Exportar Actividades"
capacidad: "pasarela.exportar_actividades.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.exportar_select"]
acciones: ["obtener_datos"]
endpoints: ["/src/pasarela/exportar_actividades_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Exportar Actividades

Propuesta generada automaticamente desde la capacidad `pasarela.exportar_actividades.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ExportarActividades. Caso de uso "exportar actividades": dado un filtro (tipo de actividad, periodo y casas), devuelve cabeceras + filas para el listado de exportación, mezclando datos de actividades con las conversiones de pasarela. Devuelve un array serializable por {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.exportar_select`

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
- `post.iactividad_val`
- `post.iasistentes_val`
- `post.id_cdc`
- `post.id_tipo_activ`
- `post.isfsv_val`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/pasarela/exportar_actividades_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
