<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 60)->nullable()->after('name');
            $table->string('gender', 20)->nullable()->after('phone');
            $table->date('birth_date')->nullable()->after('gender');
            $table->string('country', 100)->default('Indonesia')->after('address');
            $table->string('province', 100)->nullable()->after('country');
            $table->string('city', 100)->nullable()->after('province');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('postcode', 20)->nullable()->after('district');
            $table->string('address_label', 30)->default('Rumah')->after('postcode');
            $table->string('address_notes', 255)->nullable()->after('address_label');
            $table->json('saved_addresses')->nullable()->after('address_notes');
            $table->json('bank_account')->nullable()->after('saved_addresses');
            $table->json('notification_preferences')->nullable()->after('bank_account');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'username',
                'gender',
                'birth_date',
                'country',
                'province',
                'city',
                'district',
                'postcode',
                'address_label',
                'address_notes',
                'saved_addresses',
                'bank_account',
                'notification_preferences',
            ]);
        });
    }
};
