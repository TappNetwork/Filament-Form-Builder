<?php

arch('it will not use debugging functions')
    ->expect('Tapp\FilamentFormBuilder')
    ->not->toUse(['dd', 'dump', 'ray']);
