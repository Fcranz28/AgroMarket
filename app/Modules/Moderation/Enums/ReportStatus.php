<?php

namespace App\Modules\Moderation\Enums;

enum ReportStatus: string
{
    case Pending = 'pending';
    case Reviewed = 'reviewed';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pendiente',
            self::Reviewed => 'En revisión',
            self::Resolved => 'Resuelto',
            self::Dismissed => 'Descartado',
        };
    }
}
