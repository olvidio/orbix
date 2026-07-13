---
id: "usuarios.usuario_grupo_del_lst.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Grupo Del Lst"
capacidad: "usuarios.usuario_grupo_del_lst.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_grupo_del_lst"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_del_lst"]
estado_revision: "revisado"
---

# Flujo - Usuario Grupo Del Lst

## Objetivo De Usuario

Lista grupos ya asignados al usuario con acción quitar.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_grupo_del_lst`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_usuario`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_grupo_del_lst`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
