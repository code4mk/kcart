<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKcartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kcarts', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->integer('auth_user')->nullable();
            $table->string('coupon')->nullable();
            $table->float('price',18,2)->nullable();
            $table->float('discount',18,2)->nullable();
            $table->float('cprice',18,2)->nullable();
            $table->float('tax',18,2)->nullable();
            $table->float('tax_rate',18,2)->nullable();
            $table->string('shipping_area')->nullable();
            $table->float('shipping_cost',18,2)->nullable();
            $table->float('total',18,2)->nullable();
            $table->boolean('paid')->default(false);
            $table->string('affiliate')->nullable();
            $table->timestamps();
        });
        \DB::statement('ALTER TABLE kcarts AUTO_INCREMENT = 10000000');

        Schema::create('kcart_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('kcart_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('order_limit')->nullable();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('image')->nullable();
            $table->float('single_price',16,2)->nullable();
            $table->float('price',16,2)->nullable();
            $table->string('coupon')->nullable();
            $table->string('coupon_type')->nullable();
            $table->float('coupon_amount',16,2)->nullable();
            $table->float('discount',16,2)->nullable();
            $table->float('final_price',16,2)->nullable();
            $table->timestamps();
        });

        Schema::create('kcart_utsob_discount', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->float('buy',16,2)->nullable();
            $table->float('discount',16,2)->nullable();
            $table->string('dis_type')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('kcart_shipping_address', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('kcart_id')->nullable();
            $table->integer('customer_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->integer('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();
            $table->string('road')->nullable();
            $table->string('post_code')->nullable();
            $table->timestamps();
        });

        Schema::create('kcart_wishlist', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('product_id')->nullable();
            $table->integer('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('kcart_payment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('gateway')->nullable();
            $table->integer('method')->nullable();
            $table->integer('kcart_id')->nullable();
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
        Schema::dropIfExists('kcarts');
        Schema::dropIfExists('kcart_items');
        Schema::dropIfExists('kcart_wishlist');
        Schema::dropIfExists('kcart_payment');
        Schema::dropIfExists('kcart_utsob_discount');
        Schema::dropIfExists('kcart_shipping_address');
    }
}
