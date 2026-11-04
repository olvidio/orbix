---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "procesos"
titulo: "Definir un permiso de actividades de un usuario"
flujo: "procesos.usuario_perm_activ.gestionar.flujo"
preguntas: ["Como definir un permiso de actividades de un usuario en Definir un permiso de actividades de un usuario?"]
pantallas_principales: ["procesos.pantalla.usuario_perm_activ"]
fragmentos: ["procesos.pantalla.usuario_perm_activ"]
endpoints: ["/src/procesos/usuario_perm_activ_data"]
source: "docs/catalogo/procesos/flujos/usuario_perm_activ.md"
estado_revision: "generado"
---

# Ayuda IA - Definir un permiso de actividades de un usuario

Usa este documento para responder preguntas de usuario sobre como trabajar con `Definir un permiso de actividades de un usuario`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como definir un permiso de actividades de un usuario en Definir un permiso de actividades de un usuario?

## Donde Entrar

- Usuario Perm Activ (`procesos.pantalla.usuario_perm_activ`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Definir un permiso de actividades de un usuario

1. En la ficha del usuario, pulsar **añadir permiso**.
2. Elegir el tipo de actividad y el ámbito (propia dl u otras).
3. Elegir la fase de referencia y el permiso.
4. Guardar. Manda el nivel más concreto («sv n crt» sobre «sv n»).

Referencias tecnicas para verificar la respuesta:
- `/src/procesos/usuario_perm_activ_data`

## Pantallas Y Fragmentos Relacionados

- `procesos.pantalla.usuario_perm_activ`
- `procesos.pantalla.usuario_perm_activ`

## Objetivo

Dar de alta o modificar un permiso de actividades de un usuario: tipo de actividad, ámbito, fase y permiso.

## Errores Documentados

- `_(ninguno documentado)_`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
