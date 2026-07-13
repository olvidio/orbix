---
id: "usuarios.recuperar_2fa_mail.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Recuperar 2fa Mail"
capacidad: "usuarios.recuperar_2fa_mail.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/recuperar_2fa_mail"]
estado_revision: "revisado"
---

# Flujo - Recuperar 2fa Mail

## Objetivo De Usuario

Recuperación 2FA: genera código/link y envía mail al usuario.

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

- `/src/usuarios/recuperar_2fa_mail`

## Errores Conocidos

- `Esquema no válido`
- `No hay email asociado a este usuario`
- `Error al enviar el correo electrónico`
- `No se encontró ningún usuario con ese nombre`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
