<?php

namespace Punch\NevoboBundle\Model;

class Team
{
    public string $code;
    public string $naam;
    public string $sortable_rank;
    public string $standpositietekst;

    public function getRank(): int
    {
        return (int) explode('e', $this->standpositietekst, 1)[0] ?? -1;
    }
}
