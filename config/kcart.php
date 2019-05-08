<?php
return [
  // tax
  "tax" => 0.1,
  // shipping cost free upto buy
  "ship_free_upto" => 200000,
  // if shipping area <area/inout picK one for true>
  "shipping_area" => false,
  // if shipping outsite /inside <area/inout picK one for true>
  "shipping_in_out" => true,
  // specific area's shipping costs
  "shipping_area_cost" => [
    "rajshahi" => 90,
    "dhaka" => 30,
    "sylhet" => 90,
  ],
  // inside specific area or outside that area shipping cost
  "shipping_inout_cost" => [
    "inside" => 30,
    "outside" => 90
  ],
];
