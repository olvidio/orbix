---
id: "dossiers.dossiers_lista_fichas.gestionar"
tipo: "capacidad"
modulo: "dossiers"
nombre: "Gestionar Dossiers Lista Fichas"
entidades: ["DossiersListaFichas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_lista_fichas_data"]
pantallas: ["frontend/dossiers/controller/lista_dossiers.php"]
casos_uso: ["src\\dossiers\\application\\DossiersListaFichasData"]
tags: ["data", "dossiers", "dossiers_lista_fichas", "fichas", "lista"]
estado_revision: "generado"
---

# Gestionar Dossiers Lista Fichas

Propuesta generada automaticamente a partir de endpoints con prefijo comun `dossiers_lista_fichas`.

## Objetivo Funcional

Gestiona DossiersListaFichas. Filas de la tabla de relación de dossiers (modo lista en dossiers_ver). href_ver / href_abrir se firman en el borde HTTP (ver dossiers_lista_fichas_data.php).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/dossiers/dossiers_lista_fichas_data`

## Pantallas Relacionadas

- `frontend/dossiers/controller/lista_dossiers.php`

## Casos De Uso Detectados

- `src\dossiers\application\DossiersListaFichasData`

## Pistas Desde Endpoints

- Filas de la tabla de relación de dossiers (modo lista en dossiers_ver). `href_ver` / `href_abrir` se firman en el borde HTTP (ver `dossiers_lista_fichas_data.php`).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
