---
id: "misas.ver_encargos_centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Encargos Centros"
capacidad: "misas.ver_encargos_centros.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_encargos_centros"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_encargos_centros_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Encargos Centros

Propuesta generada automaticamente desde la capacidad `misas.ver_encargos_centros.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerEncargosCentros. Devuelve los datos del SlickGrid de EncargoCtr (encargos visibles para cada centro de una zona) + los desplegables estaticos del modal de edicion (zonas posibles para filtrar encargos, centros de la zona). El desplegable dinamico de encargos (que cambia al seleccionar zona en el modal) no se incluye aqui: el frontend lo pide por separado a DesplegableEncargosData cuando el usuario lo necesita.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_encargos_centros`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ctr`
- `form.id_enc`
- `form.id_item`
- `form.id_zona`
- `html.nuevo`
- `post.id_zona`

Acciones JavaScript:
- `fnjs_construir_desplegable`
- `fnjs_nuevo`
- `fnjs_prepara_select_encargo`
- `fnjs_refresh_grid`

## Endpoints Del Flujo

- `/src/misas/ver_encargos_centros_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
