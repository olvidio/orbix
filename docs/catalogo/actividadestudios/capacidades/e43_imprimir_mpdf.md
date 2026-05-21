---
id: "actividadestudios.e43_imprimir_mpdf.gestionar"
tipo: "capacidad"
modulo: "actividadestudios"
nombre: "Gestionar E43 Imprimir Mpdf"
entidades: ["E43Certificado"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
pantallas: ["frontend/actividadestudios/controller/e43_imprimir_mpdf.php"]
casos_uso: ["src\\actividadestudios\\application\\E43CertificadoData"]
tags: ["actividadestudios", "data", "e43", "e43_imprimir_mpdf", "imprimir", "mpdf"]
estado_revision: "generado"
---

# Gestionar E43 Imprimir Mpdf

Propuesta generada automaticamente a partir de endpoints con prefijo comun `e43_imprimir_mpdf`.

## Objetivo Funcional

Gestiona E43Certificado. Datos certificado E43 (pantalla e imprimible).

## Acciones Detectadas

- `obtener_datos`

## Endpoints

- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Pantallas Relacionadas

- `frontend/actividadestudios/controller/e43_imprimir_mpdf.php`

## Casos De Uso Detectados

- `src\actividadestudios\application\E43CertificadoData`

## Pistas Desde Endpoints

- Datos certificado E43 (pantalla e imprimible).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
