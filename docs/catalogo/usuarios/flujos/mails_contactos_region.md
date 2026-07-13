---
id: "usuarios.mails_contactos_region.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Mails Contactos Region"
capacidad: "usuarios.mails_contactos_region.gestionar"
pantallas_principales: ["usuarios.pantalla.mails_contactos_region"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/mails_contactos_region"]
estado_revision: "revisado"
---

# Flujo - Mails Contactos Region

## Objetivo De Usuario

Devuelve contactos email de usuarios regionales con permisos de oficina relevantes (pantalla recuperación).

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
- `get.region`
- `post.region`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/mails_contactos_region`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
