<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use TheBachtiarz\Party\Bmkg\Interfaces\Models\EarthQuakeInterface;
use TheBachtiarz\Party\Contact\Interfaces\Models\ContactInterface;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(EarthQuakeInterface::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->text(EarthQuakeInterface::ATTRIBUTE_BODY)->fulltext();
            $table->boolean(EarthQuakeInterface::ATTRIBUTE_SENT)->default(false);
            $table->timestamps();
        });

        Schema::create(ContactInterface::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string(ContactInterface::ATTRIBUTE_IDENTITY)->unique();
            $table->string(ContactInterface::ATTRIBUTE_TYPE)->default(ContactInterface::TYPE_PERSON);
            $table->boolean(ContactInterface::ATTRIBUTE_NOTIFY)->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(EarthQuakeInterface::TABLE_NAME);
        Schema::dropIfExists(ContactInterface::TABLE_NAME);
    }
};
