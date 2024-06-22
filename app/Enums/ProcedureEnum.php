<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum  ProcedureEnum: string implements HasLabel {
    case MAINTENANCE = 'maintenance';
    case MOVE = 'move';
    case RENEW = 'renew';
    case SELLING = 'selling';
    case LEASING = 'leasing';
    case WASTE = 'waste';
    case OTHER = 'other';
    case OTHER2 = 'other2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::MAINTENANCE   => 'Maintenance',
            self::MOVE          => 'Move',
            self::RENEW         => 'Renew',
            self::SELLING       => 'Selling',
            self::LEASING       => 'Leasing',
            self::WASTE         => 'Waste',
            self::OTHER         => 'Other',
            self::OTHER2        => 'Other2'
        };
    }
}
