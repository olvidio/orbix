-- Notas con acta de una región sin esquema Orbix (cg, crAfom, crB, crCam, crCoe):
-- tipo_acta 1 (acta) → 2 (certificado).
-- Esas regiones ya no se ofrecen como sigla de acta en el formulario de nota
-- (ActaPersonaFormListas): al editar una nota antigua en formato acta el guardado
-- la rechaza con «Para actas de otra delegación o región use el formato certificado».
-- Solo cambia tipo_acta; el texto del acta no se toca.
-- Sobre la tabla padre: corrige todas las hijas. Idempotente.

WITH cambiadas AS (
    UPDATE publicf.e_notas
    SET tipo_acta = 2
    WHERE tipo_acta = 1
      AND lower(split_part(btrim(acta), ' ', 1)) ~ '^(cg|crafom|crb|crcam|crcoe)f?$'
    RETURNING 1
)
SELECT public.migracion_aviso(format(
    'e_notas: %s notas de regiones sin esquema pasadas de acta a certificado',
    (SELECT count(*) FROM cambiadas)
));
