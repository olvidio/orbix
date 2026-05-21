---
id: "actividadescentro.centro_encargado.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centro Encargado"
capacidad: "actividadescentro.centro_encargado.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["eliminar"]
endpoints: ["/src/actividadescentro/centro_encargado_eliminar"]
estado_revision: "generado"
---

# Flujo - Gestionar Centro Encargado

Propuesta generada automaticamente desde la capacidad `actividadescentro.centro_encargado.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CentroEncargado. Elimina un CentroEncargado de una actividad.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/actividadescentro/centro_encargado_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centro_encargado_eliminar`

## Errores Conocidos

- ``el centro encargado ya no existe``
- ``hay un error, no se ha eliminado el centro``
- ``no se sabe cual borrar``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
