---
id: "configuracion.parametros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Gestionar Parametros"
capacidad: "configuracion.parametros.gestionar"
pantallas_principales: []
fragmentos: ["configuracion.pantalla.parametros"]
acciones: ["crear_actualizar", "listar"]
endpoints: ["/src/configuracion/parametros_lista", "/src/configuracion/parametros_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Parametros

Propuesta generada automaticamente desde la capacidad `configuracion.parametros.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Parametros. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `configuracion.pantalla.parametros`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/configuracion/parametros_update`

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/configuracion/parametros_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.fin_dia`
- `form.fin_mes`
- `form.ini_dia`
- `form.ini_mes`
- `form.valor`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/configuracion/parametros_lista`
- `/src/configuracion/parametros_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
