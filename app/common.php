<?php
use app\orders\model\User;

function getNicknames($user_ids) 
{
  try {
    $idsArr =  json_decode($user_ids);
    $names = '';
    foreach ($idsArr as $id) {
      $names .= User::where('id', $id)->value('nickname') . ', ';
    }
    return substr($names, 0, -2);
  } catch(Exception $e) {
    echo $e->getMessage();
  }
}


function getNickname($user_id)
{
  try {
   return User::where('id', $user_id)->value('nickname');
  } catch (Exception $e) {
    echo $e->getMessage();
  }
}
