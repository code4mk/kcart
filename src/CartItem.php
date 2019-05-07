<?php

namespace Code4mk\Kcart;

use Code4mk\Kcart\Model\Kcart;
use Code4mk\Kcart\Model\KcartItem;

class CartItem
{
  // add a product
  public function add($cartID,$productID,$title,$quantity,$size,$image,$price)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->where('product_id',$productID)
                          ->first();

    if(is_null($product)){
      $productPrice = $price * $quantity;
      $item = new KcartItem;
      $item->kcart_id = $cartID;
      $item->product_id = $productID;
      $item->title = $title;
      $item->quantity = $quantity;
      $item->size = $size;
      $item->image = $image;
      $item->single_price = $price;
      $item->price = $productPrice;
      $item->final_price = $productPrice;
      $item->save();
    } else{
      $quantity = $product->quantity + $quantity;
      $price = $product->single_price * $quantity;
      $product->quantity = $quantity;
      $product->price = $price;

      if($product->coupon){
        if($product->coupon_type === 'per'){
          $discount = ($product->coupon_amount * $price ) / 100;
          $fprice = $price - $discount;
          $product->discount = $discount;
          $product->final_price = $fprice;
        }else{
          $discount = $quantity * $product->coupon_amount ;
          $fprice = $price - $discount;
          $product->discount = $discount;
          $product->final_price = $fprice;
        }
      }else{
        $product->final_price = $price;
      }
      $product->save();
    }
    // cart auto update
    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();
  }
  // redeem coupon for single product
  public function haveCoupon($cartID,$productID,$code,$type,$amount)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->where('product_id',$productID)
                          ->first();
    $product->coupon = $code;
    $product->coupon_type = $type;
    $product->coupon_amount = $amount;
    $product->save();
    if($product->coupon){
      if($product->coupon_type === 'per'){
        $discount = ($product->coupon_amount * $product->price ) / 100;
        $fprice = $product->price - $discount;
        $product->discount = $discount;
        $product->final_price = $fprice;
      }else{
        $discount = $product->quantity * $product->coupon_amount ;
        $fprice = $product->price - $discount;
        $product->discount = $discount;
        $product->final_price = $fprice;
      }
    }else{
      $product->final_price = $price;
    }
    $product->save();

    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();
    dd($updateCart);
  }
  // remove redeem single product
  public function haveCouponRemove($cartID,$productID)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->where('product_id',$productID)
                          ->first();
    $product->coupon = null;
    $product->coupon_type = null;
    $product->coupon_amount = null;
    $product->discount = null;
    $product->save();

    $product->final_price = $product->price;
    $product->save();

    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();
  }
  // update product
  public function update($cartID,$productID,$quantity)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->where('product_id',$productID)
                          ->first();
    $product->quantity = $quantity;
    $price = $product->single_price * $quantity;
    $product->price = $price;
    if($product->coupon){
      if($product->coupon_type === 'per'){
        $discount = ($product->coupon_amount * $price ) / 100;
        $fprice = $price - $discount;
        $product->discount = $discount;
        $product->final_price = $fprice;
      }else{
        $discount = $quantity * $product->coupon_amount ;
        $fprice = $price - $discount;
        $product->discount = $discount;
        $product->final_price = $fprice;
      }
    }else{
      $product->final_price = $price;
    }

    $product->save();

    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();

  }
  // remove product
  public function remove($cartID,$productID)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->where('product_id',$productID)
                          ->first();

    $product->delete();

    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();
  }

  // remove all product
  public function removeAll($cartID)
  {
    $product = KcartItem::where('kcart_id',$cartID)
                          ->delete();

    $cartItems = KcartItem::where('kcart_id',$cartID)->get();
    $allprice = 0;
    foreach ($cartItems as $key => $value) {
      $allprice = $allprice + $value{'final_price'};
    }
    $updateCart = Kcart::where('id',$cartID)->first();
    $updateCart->price = $allprice;
    $updateCart->save();
  }
}
