---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubis"
titulo: "Buscar un centro o una casa"
flujo: "ubis.ubis_buscar.gestionar.flujo"
preguntas: ["Como buscar un centro o una casa en Buscar un centro o una casa?", "Como buscar con más opciones en Buscar un centro o una casa?"]
pantallas_principales: ["ubis.pantalla.ubis_buscar"]
fragmentos: ["ubis.pantalla.ubis_buscar"]
endpoints: ["/src/ubis/ubis_buscar_data"]
source: "docs/catalogo/ubis/flujos/ubis_buscar.md"
estado_revision: "generado"
---

# Ayuda IA - Buscar un centro o una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Buscar un centro o una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como buscar un centro o una casa en Buscar un centro o una casa?
- Como buscar con más opciones en Buscar un centro o una casa?

## Donde Entrar

- Ubis Buscar (`ubis.pantalla.ubis_buscar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Buscar un centro o una casa

1. Entrar en **buscar**. Aparece el formulario **Buscar centro o casa**.
2. Escribir parte del **Nombre de la casa o centro**, la **Población**, o elegir **Región** o **País**. Se puede marcar «buscar ubis fuera de uso» para incluir los dados de baja.
3. Pulsar **buscar**: aparece la tabla de resultados.
4. Pulsar el nombre para abrir la ficha.
5. Si no hay resultados sale «no existe este centro o casa».

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_buscar_data`

## Buscar con más opciones

1. En el formulario, pulsar **buscar otras opciones**.
2. Elegir **Tipo** (Centro, Casa o Todos) y **Localización** (de la propia r/dl, de otra r/dl o todos), además del nombre y la población.
3. Pulsar **buscar**.

Referencias tecnicas para verificar la respuesta:
- `/src/ubis/ubis_buscar_data`

## Pantallas Y Fragmentos Relacionados

- `ubis.pantalla.ubis_buscar`
- `ubis.pantalla.ubis_buscar`

## Objetivo

Buscar un centro o una casa por nombre, población, región o país, y llegar a su ficha.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
