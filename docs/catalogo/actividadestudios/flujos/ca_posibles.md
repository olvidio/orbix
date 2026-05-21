---
id: "actividadestudios.ca_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Ca Posibles"
capacidad: "actividadestudios.ca_posibles.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.ca_posibles"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/ca_posibles_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ca Posibles

Propuesta generada automaticamente desde la capacidad `actividadestudios.ca_posibles.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CaPosibles. Misma lógica que frontend/.../ca_posibles.php; respuesta serializable. En modo lista, pagina_link_spec lo firma el front ({.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.ca_posibles`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.observ`
- `post.ca_estudios`
- `post.ca_repaso`
- `post.ca_todos`
- `post.empiezamax`
- `post.empiezamin`
- `post.grupo_estudios`
- `post.id_ctr_agd`
- `post.id_ctr_n`
- `post.idca`
- `post.na`
- `post.obj_pau`
- `post.periodo`
- `post.ref`
- `post.sel`
- `post.stack`
- `post.texto`
- `post.year`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/actividadestudios/ca_posibles_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
