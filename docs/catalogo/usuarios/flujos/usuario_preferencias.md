---
id: "usuarios.usuario_preferencias.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Preferencias"
capacidad: "usuarios.usuario_preferencias.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.preferencias"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_preferencias"]
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Preferencias

Propuesta generada automaticamente desde la capacidad `usuarios.usuario_preferencias.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioPreferencias. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.preferencias`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.estilo_color`
- `form.idioma_nou`
- `form.inicio`
- `form.layout`
- `form.oficina`
- `form.ordenApellidos`
- `form.tipo_menu`
- `form.tipo_tabla`
- `form.zona_horaria_nou`

Acciones JavaScript:
- `button:guardar preferencias`
- `fnjs_guardar_preferencias`
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/usuario_preferencias`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
