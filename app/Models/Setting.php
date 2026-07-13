<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Ambil nilai pengaturan berdasarkan key. Jika tidak ditemukan, return nilai default.
     *
     * @param  mixed  $default
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        $setting = self::find($key);

        return $setting ? $setting->value : $default;
    }

    /**
     * Simpan atau perbarui nilai pengaturan berdasarkan key.
     *
     * @param  mixed  $value
     */
    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value !== null ? (string) $value : null]
        );
    }
}
