---
id: "usuarios.usuario_grupo_lst.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Grupo Lst"
capacidad: "usuarios.usuario_grupo_lst.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_grupo_lst"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_lst"]
estado_revision: "revisado"
---

# Flujo - Usuario Grupo Lst

## Objetivo De Usuario

Lista grupos disponibles para asignar al usuario (id ~ ^5, excluye ya asignados).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_grupo_lst`

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

- `/src/usuarios/usuario_grupo_lst`

## Errores Conocidos

- `Usuario no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
