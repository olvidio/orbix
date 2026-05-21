---
id: "encargossacd.encargo_ver.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Encargo Ver"
entidades: ["EncargoVer"]
acciones: ["crear", "eliminar", "obtener_datos"]
endpoints: ["/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_eliminar", "/src/encargossacd/encargo_ver_nuevo"]
pantallas: ["frontend/encargossacd/controller/encargo_select.php", "frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoVerData", "src\\encargossacd\\application\\EncargoVerEliminar", "src\\encargossacd\\application\\EncargoVerNuevo"]
tags: ["data", "eliminar", "encargo", "encargo_ver", "encargossacd", "nuevo", "ver"]
estado_revision: "generado"
---

# Gestionar Encargo Ver

Propuesta generada automaticamente a partir de endpoints con prefijo comun `encargo_ver`.

## Objetivo Funcional

Gestiona EncargoVer. Alta de encargo desde el formulario de encargo_ver (antes encargo_ajax.php que=nuevo). Borrado desde lista encargo_select (antes encargo_ajax.php que=eliminar). Datos para la pantalla encargo_ver (nuevo / editar encargo). El frontend arma los frontend\shared\web\Desplegable a partir de los arrays devueltos.

## Acciones Detectadas

- `crear`
- `eliminar`
- `obtener_datos`

## Endpoints

- `/src/encargossacd/encargo_ver_data`
- `/src/encargossacd/encargo_ver_eliminar`
- `/src/encargossacd/encargo_ver_nuevo`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/encargo_select.php`
- `frontend/encargossacd/controller/encargo_ver.php`

## Casos De Uso Detectados

- `src\encargossacd\application\EncargoVerData`
- `src\encargossacd\application\EncargoVerEliminar`
- `src\encargossacd\application\EncargoVerNuevo`

## Pistas Desde Endpoints

- Alta de encargo desde el formulario de `encargo_ver` (antes `encargo_ajax.php` que=nuevo).
- Borrado desde lista `encargo_select` (antes `encargo_ajax.php` que=eliminar).
- Datos para la pantalla `encargo_ver` (nuevo / editar encargo). El frontend arma los `frontend\shared\web\Desplegable` a partir de los arrays devueltos.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
