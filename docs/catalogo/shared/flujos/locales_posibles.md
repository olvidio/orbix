---
id: "shared.locales_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Elegir un idioma en un desplegable"
capacidad: "shared.locales_posibles.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["cargar_desplegable"]
endpoints: ["/src/shared/locales_posibles"]
estado_revision: "revisado"
---

# Flujo - Cargar locales/idiomas

## Objetivo De Usuario

Cargar la lista de idiomas disponibles para un desplegable, en las pantallas que lo necesitan (certificados y preferencias de usuario).

## Punto De Entrada

No tiene pantalla propia: se carga sola al abrir las preferencias del usuario o los certificados emitidos.

## Escenarios Inferidos

### Elegir un idioma en un desplegable

Pasos propuestos:
1. Abrir las preferencias de usuario o un certificado emitido.
2. El desplegable de idioma se rellena con los idiomas activos.
3. Elegir el idioma que corresponda.

Endpoints asociados:
- `/src/shared/locales_posibles`

## Errores Conocidos

- Ninguno documentado.

## Ruta de menú

sin entrada de menú en el índice (endpoint embebido; las pantallas host sí tienen menú propio en
usuarios/certificados).
