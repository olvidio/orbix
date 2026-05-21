---
id: "notas.informe_stgr_agd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Informe Stgr Agd"
capacidad: "notas.informe_stgr_agd.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.informe_stgr_agd"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/informe_stgr_agd_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Informe Stgr Agd

Propuesta generada automaticamente desde la capacidad `notas.informe_stgr_agd.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona InformeStgrAgregados. Calcula el informe anual STGR de "agregados" (puntos 21..33 + x). Encapsula el uso de src\notas\application\legacy\Resumen (legacy) para que los controllers del frontend no importen la clase legacy directamente. Devuelve un array neutro {res, textos, curso_txt} listo para renderizado.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.informe_stgr_agd`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.dl`
- `post.lista`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/notas/informe_stgr_agd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
