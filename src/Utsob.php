<?php

namespace Code4mk\Kcart;

use Code4mk\Kcart\Model\Kutsob;

class Utsob
{
  public function create($title,$buy,$type,$discount)
  {
    $utsob = new Kutsob;
    $utsob->title = $title;
    $utsob->buy = $buy;
    $utsob->dis_type = $type;
    $utsob->discount = $discount;
    $utsob->save();
  }

  public function getAll()
  {
    $utsobs = Kutsob::all();
    return $utsobs;
  }

  public function get($id)
  {
    $utsob = Kutsob::find($id);
    return $utsob;
  }

  public function update($id,$title,$buy,$type,$discount)
  {
    $utsob = Kutsob::find($id);
    $utsob->title = $title;
    $utsob->buy = $buy;
    $utsob->dis_type = $type;
    $utsob->discount = $discount;
    $utsob->save();
  }

  public function active($id)
  {
    $utsob = Kutsob::where('id',$id)->first();
    $utsob->is_active = true;
    $utsob->save();
  }

  public function deactive($id)
  {
    $utsob = Kutsob::where('id',$id)->first();
    $utsob->is_active = false;
    $utsob->save();
  }

  public function delete($id)
  {
    $utsob = Kutsob::find($id);
    $utsob->delete();
  }

}
