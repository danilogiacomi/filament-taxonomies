<?php

namespace Net7\FilamentTaxonomies\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasDescription;
use BackedEnum;

enum ConceptSchemaStates: string implements HasLabel, HasDescription
{
    case DRAFT = 'DRAFT';
    case WORK_IN_PROGRESS = 'WORK_IN_PROGRESS';
    case PUBLISHED = 'PUBLISHED';

    public function getLabel(): string|null
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::WORK_IN_PROGRESS => 'Work in Progress',
            self::PUBLISHED => 'Published',
            default => null,
        };
    }

    public function getDescription(): string|null
    {
        return match ($this) {  
            self::DRAFT => 'The controlled vocabulary is in draft status.',
            self::WORK_IN_PROGRESS => 'The controlled vocabulary is in work in progress status.',
            self::PUBLISHED => 'The controlled vocabulary is published.',
            default => null,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::WORK_IN_PROGRESS => 'warning',
            self::PUBLISHED => 'success',
            default => 'gray',
        };
    }

    public static function getValues(): array
    {
        return array_map(fn (BackedEnum $enum) => $enum->value, self::cases());
    }
}
