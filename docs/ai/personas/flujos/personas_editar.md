---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "personas"
titulo: "Abrir los datos personales de una persona"
flujo: "personas.personas_editar.gestionar.flujo"
preguntas: ["Como abrir los datos personales de una persona en Abrir los datos personales de una persona?"]
pantallas_principales: ["personas.pantalla.personas_editar"]
fragmentos: ["personas.pantalla.personas_editar"]
endpoints: ["/src/personas/personas_editar_data"]
source: "docs/catalogo/personas/flujos/personas_editar.md"
estado_revision: "generado"
---

# Ayuda IA - Abrir los datos personales de una persona

Usa este documento para responder preguntas de usuario sobre como trabajar con `Abrir los datos personales de una persona`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir los datos personales de una persona en Abrir los datos personales de una persona?

## Donde Entrar

- Ficha de persona (`personas.pantalla.personas_editar`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir los datos personales de una persona

1. En la ficha pulsar **editar**. Se abre «DATOS PERSONALES» con **Delegación**, **Trato**, **Nombre**, **Apelativo**, **nx1**, **Primer apellido**, **nx2**, **Segundo Apellido**, **Lugar de nacimiento**, **Fecha Nacimiento**, **Situacion**, **Fecha situación**, **Idioma preferido**, **Profesión**, **sacd**, **Stgr**, **eap**, **inc**, **Fecha inc**, **ce**, **Lugar ce**, **Año inicio ce**, **Año fin ce** y **Observaciones**.
2. Al lado aparece el **Centro-sede**.

Referencias tecnicas para verificar la respuesta:
- `/src/personas/personas_editar_data`

## Pantallas Y Fragmentos Relacionados

- `personas.pantalla.personas_editar`
- `personas.pantalla.personas_editar`

## Objetivo

Abrir los datos personales de una persona para consultarlos o cambiarlos, o dar de alta una persona.

## Errores Documentados

- `No se ha pasado el id_nom`
- `No se encuentra la persona`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
