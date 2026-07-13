---
id: "usuarios.recuperar_password_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Recuperar Password Mail"
capacidad: "usuarios.recuperar_password_mail.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/recuperar_password_mail"]
estado_revision: "revisado"
---

# Flujo - Recuperar Password Mail

## Objetivo De Usuario

Recuperación contraseña: genera pwd temporal, marca cambio obligatorio y envía mail.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

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

- `/src/usuarios/recuperar_password_mail`

## Errores Conocidos

- `Esquema no válido`
- `Error al preparar la consulta`
- `Error al ejecutar la consulta`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `Error al actualizar la contraseña`
- `No se encontró ningún usuario con ese nombre`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
