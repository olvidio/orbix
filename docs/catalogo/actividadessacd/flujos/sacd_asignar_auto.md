---
id: "actividadessacd.sacd_asignar_auto.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd Asignar Auto"
capacidad: "actividadessacd.sacd_asignar_auto.gestionar"
pantallas_principales: ["actividadessacd.pantalla.asignar_sacd_auto"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
estado_revision: "generado"
---

# Flujo - Gestionar Sacd Asignar Auto

Propuesta generada automaticamente desde la capacidad `actividadessacd.sacd_asignar_auto.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona SacdAsignarAuto. Auto-asignacion masiva del sacd titular del centro encargado a actividades sr/sg sin sacd.

## Punto De Entrada

- `actividadessacd.pantalla.asignar_sacd_auto`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- `fnjs_asignar_sacd_auto`
- `fnjs_esc_asauto`

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_asignar_auto`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
