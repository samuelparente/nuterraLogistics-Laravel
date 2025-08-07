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
    ];

    protected $casts = [
        'notification_to' => 'array',
        'notification_cc' => 'array',
    ];

    public $timestamps = true;

    /**
     * Retorna as configurações SMTP completas.
     */
    public static function getMailSettings(): array
    {
        $settings = self::first();

        if (!$settings) {
            return [
                'transport' => env('MAIL_MAILER', 'smtp'),
                'host' => env('MAIL_HOST'),
                'port' => env('MAIL_PORT'),
                'encryption' => env('MAIL_ENCRYPTION'),
                'username' => env('MAIL_USERNAME'),
                'password' => env('MAIL_PASSWORD'),
                'from' => [
                    'address' => env('MAIL_FROM_ADDRESS'),
                    'name' => env('MAIL_FROM_NAME'),
                ],
                'to' => [],
                'cc' => [],
            ];
        }

        return [
            'transport' => 'smtp',
            'host' => $settings->smtp_host,
            'port' => $settings->smtp_port,
            'encryption' => $settings->smtp_encryption,
            'username' => $settings->smtp_user,
            'password' => $settings->smtp_password,
            'from' => [
                'address' => $settings->smtp_from_address,
                'name' => $settings->smtp_from_name,
            ],
            'to' => $settings->notification_to ?? [],
            'cc' => $settings->notification_cc ?? [],
        ];
    }

    public function toList(): array
    {
        return $this->notification_to ?? [];
    }

    public function ccList(): array
    {
        return $this->notification_cc ?? [];
    }
}
