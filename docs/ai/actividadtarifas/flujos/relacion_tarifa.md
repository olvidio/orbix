---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadtarifas"
titulo: "Asignar una tarifa a un tipo de actividad"
flujo: "actividadtarifas.relacion_tarifa.gestionar.flujo"
preguntas: ["Como asignar una tarifa a un tipo de actividad en Asignar una tarifa a un tipo de actividad?"]
pantallas_principales: ["actividadtarifas.pantalla.tarifa_tipo_actividad"]
fragmentos: ["actividadtarifas.pantalla.tarifa_tipo_actividad_form", "actividadtarifas.pantalla.tarifa_tipo_actividad_lista"]
endpoints: ["/src/actividadtarifas/relacion_tarifa_eliminar", "/src/actividadtarifas/relacion_tarifa_form_data", "/src/actividadtarifas/relacion_tarifa_lista_data", "/src/actividadtarifas/relacion_tarifa_update"]
source: "docs/catalogo/actividadtarifas/flujos/relacion_tarifa.md"
estado_revision: "generado"
---

# Ayuda IA - Asignar una tarifa a un tipo de actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Asignar una tarifa a un tipo de actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como asignar una tarifa a un tipo de actividad en Asignar una tarifa a un tipo de actividad?

## Donde Entrar

- Tarifa Tipo Actividad (`actividadtarifas.pantalla.tarifa_tipo_actividad`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Asignar una tarifa a un tipo de actividad

1. Entrar en **tarifa <-> tipos actividad**.
2. Elegir el tipo de actividad (obligatorio: «debe indicar el tipo de actividad») y la tarifa (obligatoria: «debe indicar la id_tarifa»).
3. Guardar. Para cambiar la relación existente, abrirla, elegir otra tarifa y guardar; para quitarla, eliminarla.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadtarifas/relacion_tarifa_eliminar`
- `/src/actividadtarifas/relacion_tarifa_form_data`
- `/src/actividadtarifas/relacion_tarifa_lista_data`
- `/src/actividadtarifas/relacion_tarifa_update`

## Pantallas Y Fragmentos Relacionados

- `actividadtarifas.pantalla.tarifa_tipo_actividad`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_form`
- `actividadtarifas.pantalla.tarifa_tipo_actividad_lista`

## Objetivo

Decidir qué tipo de tarifa corresponde a cada tipo de actividad.

## Errores Documentados

- `debe indicar el tipo de actividad`
- `debe indicar la tarifa`
- `hay un error, no se ha borrado`
- `hay un error, no se ha guardado`
- `no se encuentra la relación`
- `no sé cuál he de borrar`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
