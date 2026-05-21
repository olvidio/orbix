---
id: "usuarios.role_grupmenu_add.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Role Grupmenu Add"
capacidad: "usuarios.role_grupmenu_add.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_grupmenu"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_add"]
estado_revision: "generado"
---

# Flujo - Gestionar Role Grupmenu Add

Propuesta generada automaticamente desde la capacidad `usuarios.role_grupmenu_add.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona RoleGrupmenuAdd. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_grupmenu`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_role`
- `post.sel`

Acciones JavaScript:
- `fnjs_add_grupmenu`

## Endpoints Del Flujo

- `/src/usuarios/role_grupmenu_add`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
