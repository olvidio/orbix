---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "camas"
flujo: "ubiscamas.cama.gestionar.flujo"
preguntas: ["Como gestionar camas en camas?"]
pantallas_principales: ["ubiscamas.pantalla.cama_form"]
fragmentos: ["ubiscamas.pantalla.cama_form"]
endpoints: ["/src/ubiscamas/cama_delete", "/src/ubiscamas/cama_form_data", "/src/ubiscamas/cama_update"]
source: "docs/catalogo/ubiscamas/flujos/cama.md"
estado_revision: "generado"
---

# Ayuda IA - camas

Usa este documento para responder preguntas de usuario sobre como trabajar con `camas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como gestionar camas en camas?

## Donde Entrar

- Cama Form (`ubiscamas.pantalla.cama_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Gestionar camas

1. Guardar antes la habitación y pulsar **Añadir cama**.
2. Rellenar **Descripción** y marcar **VIP** o **Larga**; pulsar **Guardar**.
3. Para cambiar o quitar una cama, usar **Editar** o **Eliminar** en su fila.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/cama_delete`
- `/src/ubiscamas/cama_form_data`
- `/src/ubiscamas/cama_update`

## Pantallas Y Fragmentos Relacionados

- `ubiscamas.pantalla.cama_form`
- `ubiscamas.pantalla.cama_form`

## Objetivo

Crear, editar o eliminar camas individuales de una habitación.

## Errores Documentados

- `ID de cama no proporcionado`
- `No se encontró la cama a eliminar`
- `hay un error, no se ha eliminado la cama`
- `Error al eliminar la cama`
- `Habitación no válida`
- `Cama no válida`
- `Error al guardar la cama`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
