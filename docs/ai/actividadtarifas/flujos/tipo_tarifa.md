---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadtarifas"
titulo: "Mantener los tipos de tarifa"
flujo: "actividadtarifas.tipo_tarifa.gestionar.flujo"
preguntas: ["Como mantener los tipos de tarifa en Mantener los tipos de tarifa?"]
pantallas_principales: ["actividadtarifas.pantalla.tarifa"]
fragmentos: ["actividadtarifas.pantalla.tarifa_form", "actividadtarifas.pantalla.tarifa_lista"]
endpoints: ["/src/actividadtarifas/tipo_tarifa_eliminar", "/src/actividadtarifas/tipo_tarifa_form_data", "/src/actividadtarifas/tipo_tarifa_lista_data", "/src/actividadtarifas/tipo_tarifa_update"]
source: "docs/catalogo/actividadtarifas/flujos/tipo_tarifa.md"
estado_revision: "generado"
---

# Ayuda IA - Mantener los tipos de tarifa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Mantener los tipos de tarifa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como mantener los tipos de tarifa en Mantener los tipos de tarifa?

## Donde Entrar

- Tarifa (`actividadtarifas.pantalla.tarifa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Mantener los tipos de tarifa

1. Entrar en **definir tarifa**: aparece el listado de tarifas.
2. Para crear una, pulsar nuevo; escribir la **letra**, el **modo** y las **observaciones**, y pulsar **guardar** (o **cancel**).
3. Para modificar, pulsar la tarifa, cambiar los datos y **guardar**.
4. Para quitarla, abrirla y pulsar **eliminar**, confirmando.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/tipo_tarifa_eliminar`
- `/src/actividadtarifas/tipo_tarifa_form_data`
- `/src/actividadtarifas/tipo_tarifa_lista_data`
- `/src/actividadtarifas/tipo_tarifa_update`

## Pantallas Y Fragmentos Relacionados

- `actividadtarifas.pantalla.tarifa`
- `actividadtarifas.pantalla.tarifa_form`
- `actividadtarifas.pantalla.tarifa_lista`

## Objetivo

Mantener el catálogo de tipos de tarifa: cada uno con su letra, su modo y observaciones.

## Errores Documentados

- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la tarifa`
- `no sé cuál he de borrar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
