<?php

namespace App\Models;

use App\Concerns\HasContextualActivityLog;
use Database\Factories\SettingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Spatie\Activitylog\LogOptions;

class Setting extends Model
{
    public const TYPE_TEXT = 'text';

    public const TYPE_NUMBER = 'number';

    public const TYPE_BOOLEAN = 'boolean';

    public const TYPE_IMAGE = 'image';

    public const TYPE_SELECT = 'select';

    public const TYPE_JSON = 'json';

    public const TYPE_ENCRYPTED = 'encrypted';

    /** @var list<string> */
    public const TYPES = [
        self::TYPE_TEXT,
        self::TYPE_NUMBER,
        self::TYPE_BOOLEAN,
        self::TYPE_IMAGE,
        self::TYPE_SELECT,
        self::TYPE_JSON,
        self::TYPE_ENCRYPTED,
    ];

    use HasContextualActivityLog, HasFactory;

    public function activityLogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['group', 'key', 'value', 'type'])
            ->logOnlyDirty()
            ->useLogName('settings')
            ->setDescriptionForEvent(fn (string $event): string => "Setting {$event}")
            ->dontSubmitEmptyLogs();
    }

    /** @var list<string> */
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'created_by',
        'updated_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getValue(mixed $default = null): mixed
    {
        if ($this->value === null) {
            return $default;
        }

        $decoded = json_decode($this->value, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $default;
        }

        if ($decoded === null) {
            return $default;
        }

        if ($this->type === self::TYPE_ENCRYPTED && is_string($decoded)) {
            try {
                return Crypt::decryptString($decoded);
            } catch (\Throwable) {
                // Key changed or value tampered with — fail closed: return default
                // rather than leaking the ciphertext.
                return $default;
            }
        }

        return $decoded;
    }

    public function setValue(mixed $value): void
    {
        if ($this->type === self::TYPE_ENCRYPTED) {
            if ($value === null || $value === '') {
                $this->value = null;

                return;
            }

            $cipher = Crypt::encryptString((string) $value);
            $this->value = json_encode($cipher, JSON_UNESCAPED_SLASHES);

            return;
        }

        $this->value = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    public function scopeGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
