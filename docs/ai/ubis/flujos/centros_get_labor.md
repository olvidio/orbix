---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar o cambiar el tipo de labor de los centros"
flujo: "ubis.centros_get_labor.gestionar.flujo"
preguntas: ["Como consultar o cambiar el tipo de labor de los centros en Consultar o cambiar el tipo de labor de los centros?"]
pantallas_principales: ["ubis.pantalla.centros_get_labor"]
fragmentos: ["ubis.pantalla.centros_get_labor"]
endpoints: ["/src/ubis/centros_get_labor"]
source: "docs/catalogo/ubis/flujos/centros_get_labor.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar o cambiar el tipo de labor de los centros

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar o cambiar el tipo de labor de los centros`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar o cambiar el tipo de labor de los centros en Consultar o cambiar el tipo de labor de los centros?

## Donde Entrar

- Centros Get Labor (`ubis.pantalla.centros_get_labor`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar o cambiar el tipo de labor de los centros

1. Entrar en **modificar centros** y elegir **labor** en el desplegable; pulsar **buscar**.
2. Se muestra la lista de centros con su tipo de centro y de labor.
3. Pulsar un centro para abrir el formulario de edición.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/centros_get_labor`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.centros_get_labor`
- `ubis.pantalla.centros_get_labor`

## Objetivo

Listar los centros de la dl con su tipo de centro y tipo de labor, para editarlos.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
