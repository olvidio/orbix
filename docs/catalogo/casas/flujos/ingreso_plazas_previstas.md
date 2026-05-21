---
id: "casas.ingreso_plazas_previstas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Ingreso Plazas Previstas"
capacidad: "casas.ingreso_plazas_previstas.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.prevision_asistentes"]
acciones: ["crear_actualizar"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Ingreso Plazas Previstas

Propuesta generada automaticamente desde la capacidad `casas.ingreso_plazas_previstas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona IngresoPlazasPrevistas. Actualiza num_asistentes_previstos de un Ingreso desde la TablaEditable de prevision_asistentes.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.prevision_asistentes`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/casas/ingreso_plazas_previstas_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.mi_of`
- `form.periodo`
- `form.year`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.mi_of`
- `post.periodo`
- `post.year`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/casas/ingreso_plazas_previstas_update`

## Errores Conocidos

- ``Hay un error, no se ha guardado``
- ``no se encuentra el ingreso``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
