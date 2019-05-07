<?php

namespace Code4mk\Kcart;

use Code4mk\Kcart\Model\Kcart;
use Code4mk\Kcart\Model\KcartItem;

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

  public function tax($id,$amount)
  {
    $cart = Kcart::find($id);
    if(!is_null($cart)){
      $cart->tax = $amount;
      $cart->save();
    }
  }

  public function shipping($id,$amount)
  {
    $cart = Kcart::find($id);
    if(!is_null($cart)){
      $cart->shipping = $amount;
      $cart->save();
    }
  }

  public function paid($id,$status=true)
  {
    $cart = Kcart::find($id);
    if(!is_null($cart)){
      $cart->paid = $status;
      $cart->save();
    }
  }

  public function coupon($id,$type,$amount){
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
                  ->first(['price','coupon','discount','cprice','tax','shipping','total',]);

    $cartItems = KcartItem::where('kcart_id',$cart_id)->get();
    $shippingCost = $cart{'shipping'};
    // shipping free upto 6k
    if($cart{'price'}>=6000){
      $shippingCost = 0;
    }

    $totalPrice = 0;
    $afterCouponPrice = 0;

    if(($cart{'cprice'} + $cart{'tax'} + $shippingCost)<0){
      $totalPrice = 0;
    }else{
      $totalPrice = $cart{'cprice'} + $cart{'tax'} + $shippingCost;
    }

    if(($cart{'cprice'} + $cart{'tax'} + $shippingCost)<0){
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
        "tax" => $cart{'tax'},
        "shipping" => $shippingCost,
        "total" => $totalPrice ,
      ],
      "items" => $cartItems
    ];

    if(sizeof($cartItems->toArray())>0){
      return $myCart;
    };
  }

}
