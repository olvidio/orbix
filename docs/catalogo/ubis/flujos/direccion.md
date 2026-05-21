---
id: "ubis.direccion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direccion"
capacidad: "ubis.direccion.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direccion_update"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/direccion_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Direccion

Propuesta generada automaticamente desde la capacidad `ubis.direccion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Direccion. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direccion_update`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/ubis/direccion_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/ubis/direccion_update`

## Errores Conocidos

- ``no se encuentra el ubi``
- ``no se encuentra la dirección``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
