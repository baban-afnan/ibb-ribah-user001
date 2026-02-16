<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE agent_services MODIFY COLUMN service_type ENUM('VNIN TO NIBSS', 'bvn_search', 'bvn_modification', 'crm', 'bvn_user', 'approval_request', 'affidavit', 'nin_selfservice', 'nin_personalization', 'nin_validation', 'ipe', 'not_selected', 'nin_modification', 'first_account') DEFAULT 'not_selected'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE agent_services MODIFY COLUMN service_type ENUM('VNIN TO NIBSS', 'bvn_search', 'bvn_modification', 'crm', 'bvn_user', 'approval_request', 'affidavit', 'nin_selfservice', 'nin_personalization', 'nin_validation', 'ipe', 'not_selected', 'nin_modification') DEFAULT 'not_selected'");
    }
};
