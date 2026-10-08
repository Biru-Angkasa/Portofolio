<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['photo_path', 'photo_alt'])]
class SiteProfile extends Model
{
    public static function current(): self
    {
        return static::query()->firstOrCreate([]);
    }

    public function photoUrl(): ?string
    {
        if ($this->photo_path === null) {
            return null;
        }

        return url('/storage/'.ltrim($this->photo_path, '/'));
    }
}
