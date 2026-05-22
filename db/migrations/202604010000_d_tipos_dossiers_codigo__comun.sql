-- Campo codigo para resolución híbrida de ficheros Select/form/update (slug único, nullable).
ALTER TABLE public.d_tipos_dossiers ADD COLUMN IF NOT EXISTS codigo VARCHAR(80) NULL;

CREATE UNIQUE INDEX IF NOT EXISTS d_tipos_dossiers_codigo_unique
    ON public.d_tipos_dossiers (codigo)
    WHERE codigo IS NOT NULL AND codigo <> '';

COMMENT ON COLUMN public.d_tipos_dossiers.codigo IS 'Slug para nombres de fichero Select_codigo, form_codigo, update_codigo; si falta o no existe fichero, se usa id numérico legacy';

UPDATE public.d_tipos_dossiers SET codigo='cargos_personas_en_actividad' WHERE id_tipo_dossier=1302;
UPDATE public.d_tipos_dossiers SET codigo='cargos_de_actividad' WHERE id_tipo_dossier=3102;
UPDATE public.d_tipos_dossiers SET codigo='matriculas_de_una_actividad' WHERE id_tipo_dossier=3103;
UPDATE public.d_tipos_dossiers SET codigo='asignaturas_de_una_actividad' WHERE id_tipo_dossier=3005;
UPDATE public.d_tipos_dossiers SET codigo='actividades_de_una_persona' WHERE id_tipo_dossier=1301;
UPDATE public.d_tipos_dossiers SET codigo='matriculas_de_una_persona' WHERE id_tipo_dossier=1303;
UPDATE public.d_tipos_dossiers SET codigo='asistentes_a_una_actividad' WHERE id_tipo_dossier=3101;
UPDATE public.d_tipos_dossiers SET codigo='habitaciones_cdc' WHERE id_tipo_dossier=2006;
UPDATE public.d_tipos_dossiers SET codigo='notas_de_una_persona' WHERE id_tipo_dossier=1011;
UPDATE public.d_tipos_dossiers SET codigo='certificados_de_una_persona' WHERE id_tipo_dossier=1010;

