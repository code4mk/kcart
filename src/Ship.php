<?php

namespace Code4mk\Kcart;

use Code4mk\Kcart\Model\Kship;

class Ship
{
  public function address($cartID,$customer,$first,$last,$phone,$email,$city,$dist,$road,$postal)
  {
    $shipping = Kship::where('kcart_id',$cartID)->first();
    if(is_null($shipping)){
      $ship = new Kship;
      $ship->kcart_id = $cartID;
      $ship->customer_id = $customer;
      $ship->first_name = $first;
      $ship->last_name = $last;
      $ship->phone = $phone;
      $ship->email = $email;
      $ship->city = $city;
      $ship->district = $dist;
      $ship->road = $road;
      $ship->post_code = $postal;
      $ship->save();
    }else{
      return $shipping;
    }
  }

  public function get($cartID)
  {
    $shipping = Kship::where('kcart_id',$cartID)->first();
    return $shipping;
  }

  public function getCustomerAddresses($userID)
  {
    $shipping = Kship::where('customer_id',$userID)->get();
    return $shipping;
  }
}
