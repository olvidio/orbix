---
id: "encargossacd.ctr_get_select.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Ctr Get Select"
entidades: ["EncargoCtrSelect"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/ctr_get_select_data"]
pantallas: ["frontend/encargossacd/controller/ctr_ficha.php", "frontend/encargossacd/controller/encargo_ver.php", "frontend/encargossacd/model/DesplCentros.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoCtrSelectData"]
tags: ["ctr", "ctr_get_select", "data", "encargossacd", "get", "select"]
estado_revision: "generado"
---

# Gestionar Ctr Get Select

Propuesta generada automaticamente a partir de endpoints con prefijo comun `ctr_get_select`.

## Objetivo Funcional

Gestiona EncargoCtrSelect. Payload JSON para el desplegable de centros segun filtro (y zona opcional). Devuelve el contrato estandar definido en refactor.md (id, name, opciones, selected, blanco, val_blanco, action) para que el frontend monte el <select> con fnjs_construir_desplegable (o el modelo frontend/encargossacd/model/DesplCentros). Importante: esta clase vive en capa application y por tanto **no** puede instanciar frontend\shared\web\Desplegable (ver refactor.md).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/ctr_get_select_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/ctr_ficha.php`
- `frontend/encargossacd/controller/encargo_ver.php`
- `frontend/encargossacd/model/DesplCentros.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoCtrSelectData`

## Pistas Desde Endpoints

- Payload JSON para el desplegable de centros segun filtro (y zona opcional). Devuelve el contrato estandar definido en `refactor.md` (`id`, `name`, `opciones`, `selected`, `blanco`, `val_blanco`, `action`) para que el frontend monte el `<select>` con `fnjs_construir_desplegable` (o el modelo `frontend/encargossacd/model/DesplCentros`). Importante: esta clase vive en capa `application` y por tanto **no** puede instanciar `frontend\shared\web\Desplegable` (ver `refactor.md`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
