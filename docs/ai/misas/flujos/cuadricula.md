---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "misas"
titulo: "Cuadricula"
flujo: "misas.cuadricula.gestionar.flujo"
preguntas: ["Como crear o modificar en Cuadricula?"]
pantallas_principales: []
fragmentos: []
endpoints: ["/src/misas/cuadricula_update"]
source: "docs/catalogo/misas/flujos/cuadricula.md"
estado_revision: "generado"
---

# Ayuda IA - Cuadricula

Usa este documento para responder preguntas de usuario sobre como trabajar con `Cuadricula`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Cuadricula?

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
- `/src/misas/cuadricula_update`

## Pantallas Y Fragmentos Relacionados

- Ninguna pantalla relacionada.

## Objetivo

Asigna, actualiza o borra un EncargoDia en una celda de la cuadrícula y recalcula metadatos de color/texto para la fila SACD y la celda misa.

## Errores Documentados

- `Falta el id_item`
- `Este día tiene más de dos Misas`
- `Este día tiene dos Misas`
- `Este día no tiene ninguna Misa`
- `Tiene dos Misas a primera hora`
- `No está en la zona y tiene Misa a primera hora`
- `Está en `
- `<repositorio getErrorTxt()>`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
