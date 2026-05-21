---
id: "encargossacd.sacd_ausencias_jefe_zona.gestionar"
tipo: "capacidad"
modulo: "encargossacd"
nombre: "Gestionar Sacd Ausencias Jefe Zona"
entidades: ["SacdAusenciasJefeZona"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
pantallas: ["frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasJefeZonaData"]
tags: ["ausencias", "data", "encargossacd", "jefe", "sacd", "sacd_ausencias_jefe_zona", "zona"]
estado_revision: "generado"
---

# Gestionar Sacd Ausencias Jefe Zona

Propuesta generada automaticamente a partir de endpoints con prefijo comun `sacd_ausencias_jefe_zona`.

## Objetivo Funcional

Gestiona SacdAusenciasJefeZona. Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Pantallas Relacionadas

- `frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`

## Casos De Uso Detectados

- `src\encargossacd\application\SacdAusenciasJefeZonaData`

## Pistas Desde Endpoints

- Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (`frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
