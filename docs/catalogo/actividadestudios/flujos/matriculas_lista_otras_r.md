---
id: "actividadestudios.matriculas_lista_otras_r.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matriculas Lista Otras R"
capacidad: "actividadestudios.matriculas_lista_otras_r.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_lista_otras_r"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_lista_otras_r_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Matriculas Lista Otras R

Propuesta generada automaticamente desde la capacidad `actividadestudios.matriculas_lista_otras_r.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MatriculasListaOtrasR. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_lista_otras_r`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.apellido1`
- `html.apellido1`
- `html.btn`
- `html.mod`
- `html.pau`
- `html.refresh`
- `post.apellido1`
- `post.mod`
- `post.stack`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_buscar_por_apellidos`
- `fnjs_enviar_formulario`
- `fnjs_imp_certificado`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_lista_otras_r_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
