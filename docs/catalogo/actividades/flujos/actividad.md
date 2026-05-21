---
id: "actividades.actividad.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad"
capacidad: "actividades.actividad.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividades/actividad_eliminar", "/src/actividades/actividad_nuevo"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad

Propuesta generada automaticamente desde la capacidad `actividades.actividad.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Actividad, BorrarActividad. Endpoint backend AJAX: crea una nueva actividad a partir de los datos del formulario. Endpoint backend AJAX: elimina las actividades indicadas.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividades/actividad_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividades/actividad_eliminar`
- `/src/actividades/actividad_nuevo`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
