<?php
declare(strict_types=1);

use Whitesmoke\Database\Migration;
use Whitesmoke\Database\Schema\Blueprint;
use Whitesmoke\Database\Schema\Schema;

return new class implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 254)->unique();
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('users');
    }
};
