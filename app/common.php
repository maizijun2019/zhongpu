<?php
use app\orders\model\User;

// 获过订单操作人员的用户名
function getOperatorUsername($operator_id)
{
  $name = User::where('id', $operator_id)->value('username');
  return is_null($name) ? "" : $name;
}

// 获过订单操作人员的昵称
function getOperatorNickname($operator_id)
{
  $name = User::find($operator_id)->value('nickname');
  return is_null($name) ? "" : $name;
}

// 获取多名操作人员用户名
function getOperatorNames($user_ids)
{
  try {
    if (is_string($user_ids)) {
      $user_ids = json_decode($user_ids);
    }
    $names =  \getOperatorUserName($user_ids[0]);
    if (count($user_ids) > 1) {
      for ($i = 1; $i < count($user_ids); $i++) {
        $names .= ", " .  \getOperatorUserName($user_ids[$i]);
      }
    }
    return $names;
  } catch (Exception $e) {
    return;
  }
}