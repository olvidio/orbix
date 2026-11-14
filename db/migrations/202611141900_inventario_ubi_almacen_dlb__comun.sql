-- Almacén de inventario de dlb: parámetro de configuración `inventario_ubi_almacen`.
-- Hasta ahora el almacén era un id fijo en el código (id_ubi = 40, «dlb Magatzem cv»), que solo valía
-- para dlb. Desde que sale del parámetro del esquema (AlmacenInventarioService), sin él las pantallas
-- de equipajes (lugares para una valija, documentos libres) muestran un aviso en vez de la lista.
-- Esta migración deja a dlb como estaba: su almacén sigue siendo el 40.
-- Idempotente y no pisa un valor ya elegido en la pantalla de parámetros. Las demás dl lo eligen allí.

INSERT INTO "H-dlb".x_config_schema (parametro, valor)
SELECT 'inventario_ubi_almacen', '40'
WHERE NOT EXISTS (
    SELECT 1 FROM "H-dlb".x_config_schema WHERE parametro = 'inventario_ubi_almacen'
);

SELECT public.migracion_aviso(format(
    'H-dlb: inventario_ubi_almacen = %s',
    (SELECT valor FROM "H-dlb".x_config_schema WHERE parametro = 'inventario_ubi_almacen')
));
