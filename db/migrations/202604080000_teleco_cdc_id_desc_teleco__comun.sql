-- d_teleco_cdc: desc_teleco texto → id_desc_teleco entero (valores no numéricos pasan a observ).
UPDATE public.d_teleco_cdc
SET observ = desc_teleco,
    desc_teleco = NULL
WHERE desc_teleco !~ '^-?\d+$'
   OR desc_teleco = '';

UPDATE public.d_teleco_cdc
SET desc_teleco = NULL
WHERE desc_teleco !~ '^-?\d+$'
   OR desc_teleco = '';

ALTER TABLE public.d_teleco_cdc RENAME COLUMN desc_teleco TO id_desc_teleco;
ALTER TABLE public.d_teleco_cdc ALTER COLUMN id_desc_teleco TYPE int USING id_desc_teleco::integer;
