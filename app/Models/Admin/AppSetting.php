<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = [
        'smtp_host',
        'smtp_port',
        'smtp_user',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
        'notification_to',
        'notification_cc',
        'expiry_alert_days_login',
        'expiry_alert_days_email',
    ];

    protected $casts = [
        'notification_to'         => 'array',
        'notification_cc'         => 'array',
        'expiry_alert_days_login' => 'integer',
        'expiry_alert_days_email' => 'integer',
    ];

    public $timestamps = true;

    public static function getMailSettings(): array
    {
        $settings = self::first();

        if (!$settings) {
            return [
                'transport' => env('MAIL_MAILER', 'smtp'),
                'host'      => env('MAIL_HOST'),
                'port'      => env('MAIL_PORT'),
                'encryption'=> env('MAIL_ENCRYPTION'),
                'username'  => env('MAIL_USERNAME'),
                'password'  => env('MAIL_PASSWORD'),
                'from'      => [
                    'address' => env('MAIL_FROM_ADDRESS'),
                    'name'    => env('MAIL_FROM_NAME'),
                ],
                'to'        => [],
                'cc'        => [],
            ];
        }

        return [
            'transport' => 'smtp',
            'host'      => $settings->smtp_host,
            'port'      => $settings->smtp_port,
            'encryption'=> $settings->smtp_encryption,
            'username'  => $settings->smtp_user,
            'password'  => $settings->smtp_password,
            'from'      => [
                'address' => $settings->smtp_from_address,
                'name'    => $settings->smtp_from_name,
            ],
            'to'        => $settings->toList(),
            'cc'        => $settings->ccList(),
        ];
    }

    public function toList(): array
    {
        $value = $this->notification_to;

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            return [$value];
        }

        return [];
    }

    public function ccList(): array
    {
        $value = $this->notification_cc;

        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }

            return [$value];
        }

        return [];
    }

    // ===== Helpers novos para dias de validade =====

    public function expiryDaysForLogin(): int
    {
        // fallback 180 dias se por algum motivo estiver null
        return $this->expiry_alert_days_login ?: 180;
    }

    public function expiryDaysForEmail(): int
    {
        return $this->expiry_alert_days_email ?: 180;
    }
}
