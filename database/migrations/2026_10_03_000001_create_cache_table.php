<?php
declare(strict_types=1);

use Whitesmoke\Database\Migration;
use Whitesmoke\Database\Schema\Blueprint;
use Whitesmoke\Database\Schema\Schema;

// Used only with CACHE_DRIVER=database.
return new class implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('cache', function (Blueprint $table): void {
            $table->id();
            $table->string('cache_key', 200)->unique();
            $table->text('payload');                 // JSON
            $table->bigInteger('expires_at');        // Unix time, 0 = no expiry
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('cache');
    }
};
