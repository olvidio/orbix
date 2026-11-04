---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Anotar el ingreso de una actividad"
flujo: "casas.casa_ingreso.gestionar.flujo"
preguntas: ["Como anotar el ingreso de una actividad en Anotar el ingreso de una actividad?"]
pantallas_principales: ["casas.pantalla.casa"]
fragmentos: ["casas.pantalla.casa_ingreso_form"]
endpoints: ["/src/casas/casa_ingreso_eliminar", "/src/casas/casa_ingreso_form_data", "/src/casas/casa_ingreso_update"]
source: "docs/catalogo/casas/flujos/casa_ingreso.md"
estado_revision: "generado"
---

# Ayuda IA - Anotar el ingreso de una actividad

Usa este documento para responder preguntas de usuario sobre como trabajar con `Anotar el ingreso de una actividad`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como anotar el ingreso de una actividad en Anotar el ingreso de una actividad?

## Donde Entrar

- Casa (`casas.pantalla.casa`)

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Anotar el ingreso de una actividad

1. Abrir el estudio económico de la casa y elegir la actividad.
2. Rellenar los importes del formulario de ingreso y guardar.
3. Para quitar el ingreso, usar eliminar y confirmar.

Referencias tecnicas para verificar la respuesta:
- `/src/casas/casa_ingreso_eliminar`
- `/src/casas/casa_ingreso_form_data`
- `/src/casas/casa_ingreso_update`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.casa`
- `casas.pantalla.casa_ingreso_form`

## Objetivo

Anotar o corregir los ingresos de una actividad en una casa.

## Errores Documentados

- `Falta id_activ`
- `Actividad no encontrada`
- `Hay un error, no se ha guardado la actividad.`
- `Hay un error, no se ha guardado.`
- `no sé cuál he de borar`
- `Ingreso no encontrado`
- `Hay un error, no se ha eliminado`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
