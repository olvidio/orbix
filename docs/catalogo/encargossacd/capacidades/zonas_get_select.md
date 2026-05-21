---
id: "encargossacd.zonas_get_select.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Zonas Get Select"
entidades: ["EncargoZonasSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/zonas_get_select_data"]
pantallas: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoZonasSelectData"]
tags: ["data", "encargossacd", "get", "select", "zonas", "zonas_get_select"]
estado_revision: "generado"
---

# Gestionar Zonas Get Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `zonas_get_select`.

## Objetivo Funcional

Gestiona EncargoZonasSelect. Payload JSON para el desplegable de zonas (grupo «zonas misas»). Devuelve el contrato estandar definido en refactor.md, sin instanciar frontend\shared\web\Desplegable (responsabilidad exclusiva del frontend).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/zonas_get_select_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoZonasSelectData`

## Pistas Desde Endpoints

- Payload JSON para el desplegable de zonas (grupo «zonas misas»). Devuelve el contrato estandar definido en `refactor.md`, sin instanciar `frontend\shared\web\Desplegable` (responsabilidad exclusiva del frontend).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
