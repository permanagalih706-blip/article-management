<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->string('reason')->change();
        });
    }

    public function down(): void
    {
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->enum('reason', ['spam', 'abusive', 'harassment', 'other'])->change();
        });
    }
};
