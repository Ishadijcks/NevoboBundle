<?php

namespace Punch\NevoboBundle\Model;

use DateTime;

class Wedstrijd
{
    public int $code;
    public DateTime $datum;
    public string $dwf_url;
    public int $lengte;
    public string $status;
    public DateTime $tijd;
    public string $wedstrijdcode;
    public array $setstanden;
    public Uitslag $uitslag;
}
