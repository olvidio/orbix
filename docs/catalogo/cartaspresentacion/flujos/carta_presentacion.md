---
id: "cartaspresentacion.carta_presentacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cartaspresentacion"
nombre: "Flujo - Gestionar Carta Presentacion"
capacidad: "cartaspresentacion.carta_presentacion.gestionar"
pantallas_principales: []
fragmentos: ["cartaspresentacion.pantalla.cartas_presentacion_form"]
acciones: ["crear_actualizar", "eliminar", "ver_formulario"]
endpoints: ["/src/cartaspresentacion/carta_presentacion_eliminar", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Carta Presentacion

Propuesta generada automaticamente desde la capacidad `cartaspresentacion.carta_presentacion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CartaPresentacion. Crea / actualiza una CartaPresentacion. Datos del formulario de modificacion de una CartaPresentacion (valida permisos: solo dl propia o cr). Elimina una CartaPresentacion.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cartaspresentacion.pantalla.cartas_presentacion_form`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/cartaspresentacion/carta_presentacion_update`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/cartaspresentacion/carta_presentacion_eliminar`

### Ver Formulario

Pasos propuestos:
1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Endpoints asociados:
- `/src/cartaspresentacion/carta_presentacion_form_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.observ`
- `html.pres_mail`
- `html.pres_nom`
- `html.pres_telf`
- `html.zona`
- `post.id_direccion`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar_cp`

## Endpoints Del Flujo

- `/src/cartaspresentacion/carta_presentacion_eliminar`
- `/src/cartaspresentacion/carta_presentacion_form_data`
- `/src/cartaspresentacion/carta_presentacion_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
