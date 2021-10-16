<?php

namespace Punch\NevoboBundle\Model;

use DateTime;

class Poule
{
    public string $code;
    public string $omschrijving;
    public bool $is_stand_berekenbaar;

    // All these only exist on regular poules, not the 'Beker competitie'
    public ?int $degradatie_hoogste;
    public ?int $degradatie_laagste;
    public ?int $degradatiewedstrijden_hoogste;
    public ?int $degradatiewedstrijden_laagste;
    public ?int $handhaving_hoogste;
    public ?int $handhaving_laagste;
    public ?int $promotie_hoogste;
    public ?int $promotie_laagste;
    public ?int $promotiewedstrijden_hoogste;
    public ?int $promotiewedstrijden_laagste;
    public ?DateTime $update_timestamp;
}
