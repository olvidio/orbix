---
id: "encargossacd.sacd_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Sacd Ficha"
capacidad: "encargossacd.sacd_ficha.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ficha_ajax"]
acciones: ["crear_actualizar", "obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ficha_data", "/src/encargossacd/sacd_ficha_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Sacd Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdFicha. Datos para la ficha de encargos de un SACD (sacd_ficha_ajax?que=ficha). Porta la lectura del antiguo controlador frontend y devuelve un payload estructurado con los encargos y sus dedicaciones (horario del centro y del SACD ya calculadas como texto cuando mod_horario=3). Mutacion de la ficha de encargos de un SACD (sacd_ficha_ajax?que=update). Porta la logica del antiguo controlador frontend, haciendo la misma actualizacion de dedicaciones por modulo y de observaciones.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ficha_ajax`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/encargossacd/sacd_ficha_update`

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dedic_m`
- `form.dedic_t`
- `form.dedic_v`
- `form.enc_num`
- `form.id_tipo_enc`
- `form.mas`
- `form.observ`
- `html.dedic_m[<?= $j ?>]`
- `html.dedic_t[<?= $j ?>]`
- `html.dedic_v[<?= $j ?>]`
- `html.enc_num`
- `html.ok`
- `post.filtro_sacd`
- `post.id_nom`
- `post.que`

Acciones JavaScript:
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_mas_enc`
- `fnjs_update_div`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ficha_data`
- `/src/encargossacd/sacd_ficha_update`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
