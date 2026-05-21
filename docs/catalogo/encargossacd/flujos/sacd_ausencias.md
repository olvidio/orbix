---
id: "encargossacd.sacd_ausencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Sacd Ausencias"
capacidad: "encargossacd.sacd_ausencias.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_update"]
acciones: ["crear_actualizar"]
endpoints: ["/src/encargossacd/sacd_ausencias_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Sacd Ausencias

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdAusencias. Guarda/modifica las ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_update.php). Devuelve ['error' => bool, 'mensajes' => string] donde mensajes acumula los errores de guardado/eliminacion para mostrar al usuario.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_update`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/encargossacd/sacd_ausencias_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_update`

## Errores Conocidos

- ``no se ha encontrado el encargo del sacd``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
