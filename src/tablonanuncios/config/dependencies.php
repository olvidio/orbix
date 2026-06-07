<?php

use src\tablonanuncios\application\AnuncioDelete;
use src\tablonanuncios\domain\contracts\AnuncioRepositoryInterface;
use src\tablonanuncios\domain\TablonAnunciosParaGM;
use src\tablonanuncios\infrastructure\persistence\postgresql\PgAnuncioRepository;
use function DI\autowire;

return [
    AnuncioRepositoryInterface::class => autowire(PgAnuncioRepository::class),

    AnuncioDelete::class => autowire(AnuncioDelete::class),
    TablonAnunciosParaGM::class => autowire(TablonAnunciosParaGM::class),
];
