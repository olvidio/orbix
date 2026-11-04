---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Eliminar una telecomunicación"
flujo: "ubis.teleco.gestionar.flujo"
preguntas: ["Como eliminar una telecomunicación en Eliminar una telecomunicación?"]
pantallas_principales: ["ubis.pantalla.teleco_editar", "ubis.pantalla.teleco_tabla"]
fragmentos: []
endpoints: ["/src/ubis/teleco_eliminar", "/src/ubis/teleco_guardar"]
source: "docs/catalogo/ubis/flujos/teleco.md"
estado_revision: "generado"
---

# Ayuda IA - Eliminar una telecomunicación

Usa este documento para responder preguntas de usuario sobre como trabajar con `Eliminar una telecomunicación`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como eliminar una telecomunicación en Eliminar una telecomunicación?

## Donde Entrar

- Teleco Editar (`ubis.pantalla.teleco_editar`)
- Teleco Tabla (`ubis.pantalla.teleco_tabla`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Eliminar una telecomunicación

1. En **telecos**, marcar el registro y pulsar **eliminar**.
2. Confirmar el aviso «¿Está seguro que desea eliminar este registro?».

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/teleco_eliminar`
- `/src/ubis/teleco_guardar`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.teleco_editar`
- `ubis.pantalla.teleco_tabla`

## Objetivo

Eliminar una telecomunicación de un centro o casa.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
