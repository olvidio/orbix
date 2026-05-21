---
id: "actividadplazas.gestion_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Gestion Plazas"
capacidad: "actividadplazas.gestion_plazas.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Gestion Plazas

Propuesta generada automaticamente desde la capacidad `actividadplazas.gestion_plazas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GestionPlazas. Actualiza las plazas (totales, concedidas o pedidas) desde la edicion inline de frontend\shared\web\TablaEditable. Devuelve los datos del cuadro de gestion de plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo) para que el controller frontend monte el frontend\shared\web\TablaEditable.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/actividadplazas/gestion_plazas_update`

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.colName`
- `form.data`
- `html.refresh`
- `post.dl`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_tipo_activ`
- `post.periodo`
- `post.refresh`
- `post.sactividad`
- `post.sactividad2`
- `post.sasistentes`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/actividadplazas/gestion_plazas_data`
- `/src/actividadplazas/gestion_plazas_update`

## Errores Conocidos

- ``no se encuentra la actividad``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
