---
id: "usuarios.mails_contactos_region.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Ver los contactos de la región"
capacidad: "usuarios.mails_contactos_region.gestionar"
pantallas_principales: ["usuarios.pantalla.mails_contactos_region"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/mails_contactos_region"]
estado_revision: "revisado"
---

# Flujo - Mails Contactos Region

## Objetivo De Usuario

Mostrar los correos de contacto de los administradores de la región para pedir ayuda de acceso.

## Punto De Entrada

Pantalla de ayuda de acceso.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ver los contactos de la región

Pasos propuestos:
1. Abrir la ayuda de acceso.
2. Consultar la lista de contactos y escribir al que corresponda.

Endpoints asociados:
- `/src/usuarios/mails_contactos_region`

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
