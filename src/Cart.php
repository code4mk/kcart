<?php

namespace Code4mk\Kcart;

use Code4mk\Kcart\Model\Kcart;
use Code4mk\Kcart\Model\KcartItem;
use Code4mk\Kcart\Model\Kutsob;
use Config;

class Cart
{
  public function create($authUser)
  {
    $cart = new Kcart;
    $cart->auth_user = $authUser;
    $cart->save();
    return $cart->id;
  }

  public function userCart($authUser)
  {
    $cart = Kcart::where('auth_user',$authUser)
                  ->where('paid',false)
                  ->first();
    if($cart){
      $output = [
        "status" => true,
        "cart" => $cart->id
      ];
      return $output;
    }
  }


  public function paid($id)
  {
    $cart = Kcart::find($id);
    if(!is_null($cart)){
      $cart->paid = true;
      $cart->save();
    }
  }

  public function coupon($id,$code,$type,$amount){
    $cart = Kcart::find($id);
    if(!is_null($cart)){
      if($type === 'fix'){
        $cartPrice = $cart->price;
        $surPrice = $cartPrice - $amount;
        $cart->cprice = $surPrice;
        $cart->discount = $amount;
      }elseif ($type === 'per') {
        $cartPrice = $cart->price;
        $discount = ($amount * $cartPrice) / 100;
        $surPrice = $cartPrice - $discount;
        $cart->cprice = $surPrice;
        $cart->discount = $discount;
      }
      $cart->save();
    }
  }

  public function get($cart_id,$authUser)
  {
    $cart = Kcart::where('id',$cart_id)
                  ->where('auth_user',$authUser)
                  ->where('paid',false)
                  ->first(['price','coupon','discount','cprice','tax','shipping_area','total',]);

    $cartItems = KcartItem::where('kcart_id',$cart_id)->get();

    $shippingArea = $cart{'shipping_area'};
    $shippingCost = 10;
    $isShippingArea = false;
    $tax = 0;
    $taxAmount = Config::get('kcart.tax');

    if(!is_null($taxAmount)){
      $tax = ($cart{'price'} * $taxAmount) / 100;
    }

    if(Config::get('kcart.shipping_area')){
      $isShippingArea = true;
      $shippingCost = Config::get('kcart.shipping_area_cost.'.$cart{'shipping_area'});
    }

    if(Config::get('kcart.shipping_in_out') && !$isShippingArea){
      $shippingCost = Config::get('kcart.shipping_inout_cost.'.$cart{'shipping_area'});
    }
    // shipping free upto 6k
    if($cart{'price'}>=Config::get('kcart.ship_free_upto')){
      $shippingCost = 0;
    }

    $totalPrice = 0;
    $afterCouponPrice = 0;
    $utsob_discount = 0;

    if(!is_null($cart)){
      $utsobOffer = Kutsob::where('buy','<=',$cart{'price'})
                            //->where('is_active',true)
                            ->get();

      if(sizeof($utsobOffer) > 0){
        $max = 0;
        foreach( $utsobOffer->toArray() as $k => $v )
        {
            $max = max( array( $max, $v['buy'] ) );
        }
        $detectOffer = collect($utsobOffer->toArray())->where('buy',$max);

        if(array_values($detectOffer->toArray())[0]['dis_type'] == 'fix'){
          $utsob_discount = array_values($detectOffer->toArray())[0]['discount'];
        }else{
          $utsob_discount = ($cart{'price'} * array_values($detectOffer->toArray())[0]['discount']) / 100;
        }
      }
    }





    if(($cart{'cprice'} + $tax + $shippingCost)<0){
      $afterCouponPrice = 0;
    }else{
      $afterCouponPrice = $cart{'price'} - $cart{'discount'};
    }

    $myCart = [
      "cart" => [
        "price" => $cart{'price'},
        "coupon" => $cart{'coupon'},
        "discount" => $cart{'discount'},
        "cprice" => $afterCouponPrice ,
        "utsob" => $utsob_discount,
        "tax" => [
          "rate" => $taxAmount . "%",
          "amount" => $tax
        ],
        "shipping" => $shippingCost,
        "total" => ($afterCouponPrice + $shippingCost + $tax ) - $utsob_discount,
      ],
      "items" => $cartItems
    ];

    if(sizeof($cartItems->toArray())>0){
      return $myCart;
    };
  }



  public function store($cart_id,$authUser)
  {
    $cart = Kcart::where('id',$cart_id)
                  ->where('auth_user',$authUser)
                  ->first();

    $cartItems = KcartItem::where('kcart_id',$cart_id)->get();

    $shippingArea = $cart{'shipping_area'};
    $shippingCost = 10;
    $isShippingArea = false;
    $tax = 0;
    $taxAmount = Config::get('kcart.tax');

    if(!is_null($taxAmount)){
      $tax = ($cart{'price'} * $taxAmount) / 100;
    }

    if(Config::get('kcart.shipping_area')){
      $isShippingArea = true;
      $shippingCost = Config::get('kcart.shipping_area_cost.'.$cart{'shipping_area'});
    }

    if(Config::get('kcart.shipping_in_out') && !$isShippingArea){
      $shippingCost = Config::get('kcart.shipping_inout_cost.'.$cart{'shipping_area'});
    }
    // shipping free upto 6k
    if($cart{'price'}>=Config::get('kcart.ship_free_upto')){
      $shippingCost = 0;
    }

    $totalPrice = 0;
    $afterCouponPrice = 0;
    $utsob_discount = 0;

    if(!is_null($cart)){
      $utsobOffer = Kutsob::where('buy','<=',$cart{'price'})
                            //->where('is_active',true)
                            ->get();


      if(sizeof($utsobOffer) > 0){
        $max = 0;
        foreach( $utsobOffer->toArray() as $k => $v )
        {
            $max = max( array( $max, $v['buy'] ) );
        }
        $detectOffer = collect($utsobOffer->toArray())->where('buy',$max);

        if(array_values($detectOffer->toArray())[0]['dis_type'] == 'fix'){
          $utsob_discount = array_values($detectOffer->toArray())[0]['discount'];
        }else{
          $utsob_discount = ($cart{'price'} * array_values($detectOffer->toArray())[0]['discount']) / 100;
        }
      }
    }
    if(($cart{'cprice'} + $tax + $shippingCost)<0){
      $afterCouponPrice = 0;
    }else{
      $afterCouponPrice = $cart{'price'} - $cart{'discount'};
    }

    $cart->cprice = $afterCouponPrice;
    $cart->utsob = $utsob_discount;
    $cart->tax = $tax;
    $cart->tax_rate = $taxAmount;
    $cart->shipping_cost = $shippingCost;
    $cart->total = ($afterCouponPrice + $shippingCost + $tax ) - $utsob_discount;
    $cart->save();
  }

  public function getCart($cartID)
  {
    $cart = Kcart::where('id',$cartID)
                  ->first();
    $cartItems = KcartItem::where('kcart_id',$cartID)->get();

    $data = [
      "cart" => $cart,
      "items" => $cartItems
    ];
    return $data;
  }

  public function shipping_area($cartID,$name)
  {
    $cart = Kcart::find($cartID);
    $cart->shipping_area = $name;
    $cart->save();
  }

}
