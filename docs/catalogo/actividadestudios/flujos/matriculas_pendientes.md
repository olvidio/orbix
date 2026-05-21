---
id: "actividadestudios.matriculas_pendientes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matriculas Pendientes"
capacidad: "actividadestudios.matriculas_pendientes.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.matriculas_pendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/matriculas_pendientes_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Matriculas Pendientes

Propuesta generada automaticamente desde la capacidad `actividadestudios.matriculas_pendientes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona MatriculasPendientes. Filas para frontend/actividadestudios/controller/matriculas_pendientes.php.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.matriculas_pendientes`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.mod`
- `html.pau`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Endpoints Del Flujo

- `/src/actividadestudios/matriculas_pendientes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
