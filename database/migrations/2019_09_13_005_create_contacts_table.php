<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contacts', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('company_id')->nullable()->index('fk_contacts_company');
			$table->string('first_name', 45)->nullable();
			$table->string('last_name', 45)->nullable();
			$table->string('email', 100)->nullable();
			$table->string('description', 1000)->nullable();
			$table->string('remember', 1000)->nullable();
			$table->string('summary', 1000)->nullable();
			$table->string('phone', 45)->nullable();
			$table->string('whatsapp_id', 45)->index();
			$table->string('facebook_id', 45)->nullable();
			$table->string('instagram_id', 45)->nullable();
			$table->string('linkedin_id', 45)->nullable();
			$table->string('json_data',1000)->default('{"picurl":"images/contacts/default.png"}');
			$table->integer('status_id')->nullable()->index('fk_contacts_status')->default('2');
			$table->timestamps();
			$table->softDeletes();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('contacts');
	}

}
