---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "los encargos de una zona"
flujo: "misas.ver_encargos_zona.gestionar.flujo"
preguntas: ["Como gestionar los encargos de una zona en los encargos de una zona?"]
pantallas_principales: ["misas.pantalla.ver_encargos_zona"]
fragmentos: ["misas.pantalla.ver_encargos_zona"]
endpoints: ["/src/misas/ver_encargos_zona_data"]
source: "docs/catalogo/misas/flujos/ver_encargos_zona.md"
estado_revision: "generado"
---

# Ayuda IA - los encargos de una zona

Usa este documento para responder preguntas de usuario sobre como trabajar con `los encargos de una zona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar los encargos de una zona en los encargos de una zona?

## Donde Entrar

- Ver Encargos Zona (`misas.pantalla.ver_encargos_zona`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar los encargos de una zona

1. En **Modificar encargos**, elegir la zona.
2. Para crear, pulsar **nuevo** y rellenar el formulario: elegir el **Tipo de encargo** (por ejemplo Misa a 1ª hora, Misa no a 1ª hora, rtm con Misa a 1ª hora, rtm sin Misa, bendición) y el **Lugar**; el botón **generar** escribe el nombre del **Encargo** como «tipo (lugar)». Completar **Descripción lugar**, **Orden**, **Prioridad**, **Observaciones** e **Idioma** y pulsar **Grabar**.
3. Para modificar, pulsar el encargo, cambiar y **Grabar**.
4. Para quitar, abrirlo y pulsar **Eliminar encargo**.

Referencias tecnicas para verificar la respuesta:
- `/src/misas/ver_encargos_zona_data`

## Pantallas Y Fragmentos Relacionados

- `misas.pantalla.ver_encargos_zona`
- `misas.pantalla.ver_encargos_zona`

## Objetivo

Ver, crear, modificar y eliminar los encargos de una zona.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
