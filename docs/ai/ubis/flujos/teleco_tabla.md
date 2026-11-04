---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Consultar las telecomunicaciones"
flujo: "ubis.teleco_tabla.gestionar.flujo"
preguntas: ["Como consultar las telecomunicaciones en Consultar las telecomunicaciones?"]
pantallas_principales: ["ubis.pantalla.teleco_tabla"]
fragmentos: ["ubis.pantalla.teleco_tabla"]
endpoints: ["/src/ubis/teleco_tabla"]
source: "docs/catalogo/ubis/flujos/teleco_tabla.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las telecomunicaciones

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las telecomunicaciones`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las telecomunicaciones en Consultar las telecomunicaciones?

## Donde Entrar

- Teleco Tabla (`ubis.pantalla.teleco_tabla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las telecomunicaciones

1. En la ficha pulsar **telecos** en «Editar:».
2. Se muestra «Telecomunicaciones de un centro o casa» con Id_ubi, nombre del teleco (hoy sale su número de tipo), descripción, número o siglas y observaciones.
3. Los botones son **modificar** y **eliminar** (sobre las filas marcadas) y **nuevo**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/teleco_tabla`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.teleco_tabla`
- `ubis.pantalla.teleco_tabla`

## Objetivo

Ver las telecomunicaciones (teléfonos, fax, e-mails, web) de un centro o casa.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
