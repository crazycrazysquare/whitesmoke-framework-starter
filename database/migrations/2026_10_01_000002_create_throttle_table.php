<?php
declare(strict_types=1);

use Whitesmoke\Database\Migration;
use Whitesmoke\Database\Schema\Blueprint;
use Whitesmoke\Database\Schema\Schema;

return new class implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('throttle', function (Blueprint $table): void {
            $table->id();
            $table->string('throttle_key', 100)->unique();
            $table->integer('attempts')->default(0);
            $table->bigInteger('window_ends');
            $table->bigInteger('locked_until')->default(0);
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('throttle');
    }
};
