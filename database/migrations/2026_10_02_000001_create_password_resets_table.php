<?php
declare(strict_types=1);

use Whitesmoke\Database\Migration;
use Whitesmoke\Database\Schema\Blueprint;
use Whitesmoke\Database\Schema\Schema;

return new class implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('password_resets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users', onDelete: 'cascade');
            $table->string('token_hash', 64)->unique();  // SHA-256 of the emailed token
            $table->bigInteger('expires_at');
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('password_resets');
    }
};
