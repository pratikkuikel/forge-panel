<?php

namespace App;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case ADMIN = 'admin';
    case TEAMMATE = 'teammate';

    public function getLabel(): ?string
    {
        return ucfirst(str_replace('_', ' ', strtolower($this->value)));
    }
}
