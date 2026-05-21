---
id: "shared.locales_posibles.gestionar"
tipo: "capacidad"
modulo: "shared"
nombre: "Gestionar Locales Posibles"
entidades: ["LocalesPosibles"]
acciones: ["ejecutar"]
endpoints: ["/src/shared/locales_posibles"]
pantallas: ["frontend/certificados/controller/certificado_emitido_imprimir.php", "frontend/certificados/controller/certificado_emitido_ver.php", "frontend/usuarios/controller/preferencias.php"]
casos_uso: []
tags: ["locales", "locales_posibles", "posibles", "shared"]
estado_revision: "generado"
---

# Gestionar Locales Posibles

Propuesta generada automaticamente a partir de endpoints con prefijo comun `locales_posibles`.

## Objetivo Funcional

Gestiona LocalesPosibles. Descripcion funcional pendiente de revisar.

## Acciones Detectadas

- `ejecutar`

## Endpoints

- `/src/shared/locales_posibles`

## Pantallas Relacionadas

- `frontend/certificados/controller/certificado_emitido_imprimir.php`
- `frontend/certificados/controller/certificado_emitido_ver.php`
- `frontend/usuarios/controller/preferencias.php`

## Casos De Uso Detectados

No se han detectado casos de uso de aplicacion.

## Pistas Desde Endpoints

- Descripcion funcional pendiente de revisar.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
