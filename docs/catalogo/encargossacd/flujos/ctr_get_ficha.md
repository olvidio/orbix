---
id: "encargossacd.ctr_get_ficha.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Ctr Get Ficha"
capacidad: "encargossacd.ctr_get_ficha.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_get_ficha"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/ctr_get_ficha_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ctr Get Ficha

Propuesta generada automaticamente desde la capacidad `encargossacd.ctr_get_ficha.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CtrGetFicha. Lectura de la ficha de atencion sacerdotal de un centro. Puerto del antiguo frontend/encargossacd/controller/ctr_get_ficha.php. Devuelve arrays planos/estructurados para que el controlador frontend arme frontend\shared\web\Desplegable y la HTML sin instanciar nada de src\.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.ctr_get_ficha`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.seleccion_sacd`
- `html.ok`
- `post.id_ubi`
- `post.seleccion_sacd`

Acciones JavaScript:
- `fnjs_cambiar_lista_sacd`
- `fnjs_cerrar`
- `fnjs_crear_horario`
- `fnjs_guardar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/encargossacd/ctr_get_ficha_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
