<?php

namespace App\Models\Default;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected $appends = [
        'url',
    ];

    public function getValueByKey(string $key): ?string
    {
        return Setting::where('key', $key)->value('value');
    }

    public function getStoragePath(string $key): ?string
    {
        $value = Setting::where('key', $key)->value('value');

        $path = storage_path('/app/public/' . $value);
        if (Storage::exists($path)) {
            return $path;
        }

        return storage_path('/app/default/' . $value);
    }

    public static function getByKey(string $key): ?string
    {
        return Setting::where('key', $key)->value('value');
    }

    public static function getByKeys(array $keys)
    {
        $value = [];
        Setting::whereIn('key', $keys)->orderBy('key', 'desc')->get()
            ->map(function ($item) use (&$value) {
                $value[$item->key] = $item->value;
            });

        return $value;
    }

    public function url(): Attribute
    {
        return Attribute::make(get: fn() => $this->type == 'image' && $this->value != '' ? route('file.show', ['file' => $this->value]) : null);
    }
}
