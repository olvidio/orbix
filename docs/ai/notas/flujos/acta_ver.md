---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "notas"
titulo: "Acta Ver"
flujo: "notas.acta_ver.gestionar.flujo"
preguntas: ["Como abrir el formulario en Acta Ver?"]
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_ver"]
endpoints: ["/src/notas/acta_ver_form_data"]
source: "docs/catalogo/notas/flujos/acta_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Acta Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Acta Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como abrir el formulario en Acta Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Abrir el formulario

1. Desde el listado, elegir crear un nuevo registro o modificar uno existente.
2. Abrir el formulario asociado.
3. Comprobar que los campos cargados corresponden al registro o contexto seleccionado.

Referencias tecnicas para verificar la respuesta:
- `/src/notas/acta_ver_form_data`

## Pantallas Y Fragmentos Relacionados

- `notas.pantalla.acta_ver`

## Objetivo

Ver y editar cabecera de acta, tribunal, PDF y vínculo a actividad CA.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
