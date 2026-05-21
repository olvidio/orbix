---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadplazas"
titulo: "Gestion Plazas"
flujo: "actividadplazas.gestion_plazas.gestionar.flujo"
preguntas: ["Como crear o modificar en Gestion Plazas?", "Como obtener datos en Gestion Plazas?"]
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.gestion_plazas", "actividadplazas.pantalla.plazas_balance_dl"]
endpoints: ["/src/actividadplazas/gestion_plazas_data", "/src/actividadplazas/gestion_plazas_update"]
source: "docs/catalogo/actividadplazas/flujos/gestion_plazas.md"
estado_revision: "generado"
---

# Ayuda IA - Gestion Plazas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Gestion Plazas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Gestion Plazas?
- Como obtener datos en Gestion Plazas?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadplazas/gestion_plazas_update`

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `actividadplazas.pantalla.gestion_plazas`
- `actividadplazas.pantalla.plazas_balance_dl`

## Objetivo

Gestiona GestionPlazas. Actualiza las plazas (totales, concedidas o pedidas) desde la edicion inline de frontend\shared\web\TablaEditable. Devuelve los datos del cuadro de gestion de plazas (cabeceras, valores, a_grupo y metadatos de periodo/tipo) para que el controller frontend monte el frontend\shared\web\TablaEditable.

## Errores Documentados

- `no se encuentra la actividad`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
