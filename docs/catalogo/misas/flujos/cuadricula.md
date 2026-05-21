---
id: "misas.cuadricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Cuadricula"
capacidad: "misas.cuadricula.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["crear_actualizar"]
endpoints: ["/src/misas/cuadricula_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Cuadricula

Propuesta generada automaticamente desde la capacidad `misas.cuadricula.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Cuadricula. Use case del endpoint cuadricula_update (migracion de apps/misas/controller/cuadricula_update.php al Slice 6a). Hace dos cosas en la misma transaccion logica: 1. Upsert / delete de un EncargoDia para un dia + encargo concretos, en funcion de key (si esta vacio, se borra; si trae id_nom, se guarda o actualiza). 2. Recalcula el bloque meta que la UI usa para pintar colores y textos (disponibilidad del sacd anterior y del nuevo, numero de misas del dia, conflictos con primera hora, etc.). El codigo es una traduccion casi literal del controlador original para minimizar riesgo de regresion: la logica de negocio en si no cambia en este slice; lo unico que cambia es donde vive.

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
- `/src/misas/cuadricula_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/cuadricula_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
