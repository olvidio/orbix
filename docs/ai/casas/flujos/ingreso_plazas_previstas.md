---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "casas"
titulo: "Ingreso Plazas Previstas"
flujo: "casas.ingreso_plazas_previstas.gestionar.flujo"
preguntas: ["Como crear o modificar en Ingreso Plazas Previstas?"]
pantallas_principales: []
fragmentos: ["casas.pantalla.prevision_asistentes"]
endpoints: ["/src/casas/ingreso_plazas_previstas_update"]
source: "docs/catalogo/casas/flujos/ingreso_plazas_previstas.md"
estado_revision: "generado"
---

# Ayuda IA - Ingreso Plazas Previstas

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ingreso Plazas Previstas`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Ingreso Plazas Previstas?

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
- `/src/casas/ingreso_plazas_previstas_update`

## Pantallas Y Fragmentos Relacionados

- `casas.pantalla.prevision_asistentes`

## Objetivo

Gestiona IngresoPlazasPrevistas. Actualiza num_asistentes_previstos de un Ingreso desde la TablaEditable de prevision_asistentes.

## Errores Documentados

- `Hay un error, no se ha guardado`
- `no se encuentra el ingreso`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
