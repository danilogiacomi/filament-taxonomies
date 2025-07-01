<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Net7\FilamentTaxonomies\Enums\TaxonomyStates;
use Net7\FilamentTaxonomies\Enums\TaxonomyTypes;
use Net7\FilamentTaxonomies\Enums\UriTypes;

return new class extends Migration
{
    public function up()
    {
        Schema::create('taxonomies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name')->unique();
            $table->longText('description')->nullable();
            $table->enum('state', TaxonomyStates::names());
            $table->enum('type', TaxonomyTypes::names());
            $table->string('uri')->nullable();
        });

        Schema::create('terms', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('terms')->onDelete('set null');
            $table->string('uri');
            $table->string('exact_match_uri')->nullable();
            $table->enum('uri_type', UriTypes::names())->default(UriTypes::internal->value);
        });

        Schema::create('taxonomy_term', function (Blueprint $table) {
            $table->id();
            $table->foreignId('taxonomy_id')->constrained()->onDelete('cascade');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['taxonomy_id', 'term_id']);
        });

        Schema::create('entity_terms', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->string('taxonomy_type');
            $table->foreignId('term_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->index(['entity_type', 'entity_id']);
            $table->unique(['entity_type', 'entity_id', 'taxonomy_type', 'term_id'], 'entity_taxonomy_term_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxonomies');
        Schema::dropIfExists('terms');
        Schema::dropIfExists('taxonomy_term');
        Schema::dropIfExists('entity_terms');
    }
};
