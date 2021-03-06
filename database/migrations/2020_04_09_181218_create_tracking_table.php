<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackingTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up(string $table_name = "tracking")
	{
		try {
			// $this->down($table_name);
			Schema::connection('socialhub_mvp.tracking')->create($table_name, function(Blueprint $table)
			{
				// $table->integer('id', true);
				$table->string('id')->unique()->nullable(false);
				$table->integer('contact_id')->nullable();
				$table->integer('sended')->nullable()->default(0);
				$table->string('message_list', 5000)->nullable();
				$table->string('comentario', 5000)->nullable();
				$table->string('post_card', 45)->nullable();
				$table->dateTime('post_date')->nullable();
				$table->dateTime('end_date')->nullable();
				$table->string('service_code', 45)->nullable();
				$table->integer('items')->nullable();
				$table->string('tracking_code', 45)->unique('tracking_code_UNIQUE');
				$table->text('json_csv_data', 65535)->nullable();
				$table->integer('status_id')->nullable()->index('fk_tracking_status_idx');
				$table->text('tracking_list', 65535)->nullable();
				$table->timestamps();
			});
		} catch (\Throwable $th) {
			//throw $th;
		}
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::connection('socialhub_mvp.tracking')->drop('tracking');
	}

}
