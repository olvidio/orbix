---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadtarifas"
titulo: "Consultar las tarifas de una casa"
flujo: "actividadtarifas.tarifa_ubi.gestionar.flujo"
preguntas: ["Como consultar las tarifas de una casa en Consultar las tarifas de una casa?", "Como crear o modificar una tarifa de una casa en Consultar las tarifas de una casa?", "Como copiar las tarifas del año anterior en Consultar las tarifas de una casa?"]
pantallas_principales: ["actividadtarifas.pantalla.tarifa_ubi"]
fragmentos: ["actividadtarifas.pantalla.tarifa_ubi_form", "actividadtarifas.pantalla.tarifa_ubi_lista"]
endpoints: ["/src/actividadtarifas/tarifa_ubi_copiar", "/src/actividadtarifas/tarifa_ubi_eliminar", "/src/actividadtarifas/tarifa_ubi_form_data", "/src/actividadtarifas/tarifa_ubi_lista_data", "/src/actividadtarifas/tarifa_ubi_update", "/src/actividadtarifas/tarifa_ubi_update_inc"]
source: "docs/catalogo/actividadtarifas/flujos/tarifa_ubi.md"
estado_revision: "generado"
---

# Ayuda IA - Consultar las tarifas de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Consultar las tarifas de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como consultar las tarifas de una casa en Consultar las tarifas de una casa?
- Como crear o modificar una tarifa de una casa en Consultar las tarifas de una casa?
- Como copiar las tarifas del año anterior en Consultar las tarifas de una casa?

## Donde Entrar

- Tarifa Ubi (`actividadtarifas.pantalla.tarifa_ubi`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Consultar las tarifas de una casa

1. Entrar en **tarifas por casa y año**.
2. Elegir la casa y el año y pulsar **buscar**.
3. Se listan sus tarifas con el importe en €.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Crear o modificar una tarifa de una casa

1. En el listado, pulsar nuevo (o la tarifa a cambiar).
2. Elegir la **tarifa**, la **serie** y escribir el importe (**nuevo importe**).
3. Pulsar **guardar**; **eliminar** la quita (con confirmación).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Copiar las tarifas del año anterior

1. Si aparece el botón de copiar, pulsarlo y confirmar.
2. Aviso: hoy la operación responde «función pendiente de reimplementar» (ver D-TAR-1).

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tarifa_ubi_copiar`
- `/src/actividadtarifas/tarifa_ubi_eliminar`
- `/src/actividadtarifas/tarifa_ubi_form_data`
- `/src/actividadtarifas/tarifa_ubi_lista_data`
- `/src/actividadtarifas/tarifa_ubi_update`
- `/src/actividadtarifas/tarifa_ubi_update_inc`

## Pantallas Y Fragmentos Relacionados

- `actividadtarifas.pantalla.tarifa_ubi`
- `actividadtarifas.pantalla.tarifa_ubi_form`
- `actividadtarifas.pantalla.tarifa_ubi_lista`

## Objetivo

Consultar y mantener las tarifas de una casa para un año: importes por tipo de tarifa y serie.

## Errores Documentados

- `Operación no autorizada (cápsula HashB inválida en update/eliminar/copiar)`
- `función de copiar tarifas pendiente de reimplementar`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`
- `no sé qué casa/año tengo que copiar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
