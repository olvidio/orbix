---
id: "misas.ver_encargos_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Encargos Zona"
capacidad: "misas.ver_encargos_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Encargos Zona

Propuesta generada automaticamente desde la capacidad `misas.ver_encargos_zona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerEncargosZona. Devuelve los datos necesarios para pintar el SlickGrid de encargos de una zona + los desplegables del modal de edicion. Replica la consulta de apps/misas/controller/ver_encargos_zona.php: encargos con id_tipo_enc >= 8100 (grupo 8...) de la zona indicada, ordenados por $orden (orden, prioridad o desc_enc).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_zona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.descripcion_lugar`
- `form.encargo`
- `form.id_enc`
- `form.id_tipo_enc`
- `form.id_ubi`
- `form.id_zona`
- `form.idioma_enc`
- `form.observ`
- `form.orden`
- `form.prioridad`
- `html.nuevo`
- `post.id_zona`
- `post.orden`

Acciones JavaScript:
- `fnjs_generarNomEnc`
- `fnjs_nuevo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/ver_encargos_zona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
