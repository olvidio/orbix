---
id: "personas.persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Persona"
capacidad: "personas.persona.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["crear_actualizar", "eliminar"]
endpoints: ["/src/personas/persona_eliminar", "/src/personas/persona_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Persona

Propuesta generada automaticamente desde la capacidad `personas.persona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Persona. Endpoint JSON: elimina una persona. Endpoint JSON: guarda los datos de una persona.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/personas/persona_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/personas/persona_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/personas/persona_eliminar`
- `/src/personas/persona_update`

## Errores Conocidos

- ``No existe la clase de la persona``
- ``No se encuentra la persona``
- ``No se ha eliminado, porque no es de mi dl``
- ``No se ha pasado el id_nom``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
