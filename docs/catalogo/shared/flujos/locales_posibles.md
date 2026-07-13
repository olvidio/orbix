---
id: "shared.locales_posibles.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Cargar locales/idiomas"
capacidad: "shared.locales_posibles.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["cargar_desplegable"]
endpoints: ["/src/shared/locales_posibles"]
estado_revision: "revisado"
---

# Flujo - Cargar locales/idiomas

## Objetivo De Usuario

Obtener la lista de idiomas activos para un desplegable en pantallas que lo necesitan (certificados,
preferencias de usuario).

## Punto De Entrada

Carga auxiliar al renderizar:

- `frontend/usuarios/controller/preferencias.php`
- `frontend/certificados/controller/certificado_emitido_ver.php`
- `frontend/certificados/controller/certificado_emitido_imprimir.php`

## Escenarios

### Cargar desplegable

1. POST (vacío) a `/src/shared/locales_posibles`.
2. Usar `a_locales` (`id_locale` → nombre idioma) en la vista.

## Errores Conocidos

- Ninguno documentado.

## Ruta de menú

sin entrada de menú en el índice (endpoint embebido; las pantallas host sí tienen menú propio en
usuarios/certificados).
