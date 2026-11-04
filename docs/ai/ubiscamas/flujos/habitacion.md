---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "ubiscamas"
titulo: "Ver las habitaciones de una casa"
flujo: "ubiscamas.habitacion.gestionar.flujo"
preguntas: ["Como ver las habitaciones de una casa en Ver las habitaciones de una casa?", "Como crear una habitación en Ver las habitaciones de una casa?", "Como modificar o eliminar una habitación en Ver las habitaciones de una casa?"]
pantallas_principales: ["ubiscamas.pantalla.habitacion_form"]
fragmentos: ["ubiscamas.pantalla.habitacion_form"]
endpoints: ["/src/ubiscamas/habitacion_delete", "/src/ubiscamas/habitacion_form_data", "/src/ubiscamas/habitacion_update"]
source: "docs/catalogo/ubiscamas/flujos/habitacion.md"
estado_revision: "generado"
---

# Ayuda IA - Ver las habitaciones de una casa

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ver las habitaciones de una casa`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como ver las habitaciones de una casa en Ver las habitaciones de una casa?
- Como crear una habitación en Ver las habitaciones de una casa?
- Como modificar o eliminar una habitación en Ver las habitaciones de una casa?

## Donde Entrar

- Habitacion Form (`ubiscamas.pantalla.habitacion_form`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Ver las habitaciones de una casa

1. Buscar la casa en **direcciones > buscar** y abrir su ficha.
2. En «Relación de dossiers» pulsar **Habitaciones (camas) de una cdc**.
3. Aparece la **Relación de habitaciones** de la casa (o «no hay ninguna fila» si aún no hay).

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Crear una habitación

1. En la relación de habitaciones, pulsar **crear una nueva habitación**.
2. Se abre «Nueva habitación» con los campos **Orden**, **Nombre**, **Número de camas**, **Número de camas VIP**, **Planta**, **Tipo de lavabo** (sin especificar, NO, completo, sin ducha o exterior), **Sillón**, **Adaptada**, **Observaciones** y **Despacho**.
3. Debajo, en **Camas**, el botón **Añadir cama** permite añadir camas una a una.
4. Pulsar **Guardar *** para guardar: el asterisco indica que se añaden automáticamente las camas según el **Número de camas** puesto arriba. **Cancelar** sale sin guardar.

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Modificar o eliminar una habitación

1. Desde la relación de habitaciones, seleccionar la habitación para modificarla o eliminarla (no probado en pantalla: la casa de prueba no tenía habitaciones).

Referencias tecnicas para verificar la respuesta:
- `/src/ubiscamas/habitacion_delete`
- `/src/ubiscamas/habitacion_form_data`
- `/src/ubiscamas/habitacion_update`

## Pantallas Y Fragmentos Relacionados

- `ubiscamas.pantalla.habitacion_form`
- `ubiscamas.pantalla.habitacion_form`

## Objetivo

Dar de alta, modificar o eliminar las habitaciones de una casa, y crear sus camas.

## Errores Documentados

- `No se encontró la habitación a eliminar`
- `hay un error, no se ha eliminado la habitación`
- `Error al eliminar la habitación`
- `Habitación no válida`
- `Error al guardar la habitación`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
