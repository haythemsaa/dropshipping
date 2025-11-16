<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['client', 'supplier', 'admin'])->default('client')->after('email');
            $table->enum('status', ['active', 'pending', 'suspended', 'rejected'])->default('active')->after('role');
            $table->string('phone')->nullable()->after('email');

            // Supplier specific fields
            $table->string('business_name')->nullable()->after('status');
            $table->text('business_address')->nullable()->after('business_name');
            $table->string('business_phone')->nullable()->after('business_address');
            $table->string('business_email')->nullable()->after('business_phone');
            $table->string('business_sector')->nullable()->after('business_email');
            $table->string('business_document')->nullable()->after('business_sector');
            $table->string('logo')->nullable()->after('business_document');

            // Bank details for suppliers
            $table->string('bank_name')->nullable()->after('logo');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_rib')->nullable()->after('bank_account_number');

            // Notification preferences
            $table->boolean('sms_notifications')->default(true)->after('bank_rib');
            $table->boolean('email_notifications')->default(true)->after('sms_notifications');

            // Commission rate for platform (percentage)
            $table->decimal('commission_rate', 5, 2)->default(10.00)->after('email_notifications');

            $table->timestamp('approved_at')->nullable()->after('commission_rate');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn([
                'role', 'status', 'phone', 'business_name', 'business_address',
                'business_phone', 'business_email', 'business_sector',
                'business_document', 'logo', 'bank_name', 'bank_account_number',
                'bank_rib', 'sms_notifications', 'email_notifications',
                'commission_rate', 'approved_at'
            ]);
        });
    }
};
