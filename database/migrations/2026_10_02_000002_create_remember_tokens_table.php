<?php
declare(strict_types=1);

use Whitesmoke\Database\Migration;
use Whitesmoke\Database\Schema\Blueprint;
use Whitesmoke\Database\Schema\Schema;

return new class implements Migration
{
    public function up(Schema $schema): void
    {
        $schema->create('remember_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users', onDelete: 'cascade');
            $table->string('selector', 24)->unique();          // finds the row
            $table->string('validator_hash', 64);              // SHA-256 of the cookie's secret
            $table->string('password_fingerprint', 64);        // revoked when the password changes
            $table->bigInteger('expires_at');
        });
    }

    public function down(Schema $schema): void
    {
        $schema->dropIfExists('remember_tokens');
    }
};
