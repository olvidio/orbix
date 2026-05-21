---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Sacd Ausencias"
flujo: "encargossacd.sacd_ausencias.gestionar.flujo"
preguntas: ["Como crear o modificar en Sacd Ausencias?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.sacd_ausencias_update"]
endpoints: ["/src/encargossacd/sacd_ausencias_update"]
source: "docs/catalogo/encargossacd/flujos/sacd_ausencias.md"
estado_revision: "generado"
---

# Ayuda IA - Sacd Ausencias

Usa este documento para responder preguntas de usuario sobre como trabajar con `Sacd Ausencias`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Sacd Ausencias?

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
- `/src/encargossacd/sacd_ausencias_update`

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.sacd_ausencias_update`

## Objetivo

Gestiona SacdAusencias. Guarda/modifica las ausencias de un SACD (frontend/encargossacd/controller/sacd_ausencias_update.php). Devuelve ['error' => bool, 'mensajes' => string] donde mensajes acumula los errores de guardado/eliminacion para mostrar al usuario.

## Errores Documentados

- `no se ha encontrado el encargo del sacd`

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Usar la seccion "Errores Documentados" cuando el usuario reporte un mensaje conocido.
