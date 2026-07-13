---
id: "usuarios.usuario_guardar_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Guardar Mail"
capacidad: "usuarios.usuario_guardar_mail.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_guardar_mail"]
estado_revision: "revisado"
---

# Flujo - Usuario Guardar Mail

## Objetivo De Usuario

Actualiza email del usuario (preferencias o admin).

## Punto De Entrada

Menú Legacy: menú usuario > preferencias. Pills2: menú usuario > preferencias.

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
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_guardar_mail`

## Errores Conocidos

- `Usuario no encontrado`
- `hay un error, no se ha guardado`

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
