<?php
namespace App\Library; use App\Order; use App\User; use App\FundRecord; use Illuminate\Support\Facades\DB; class FundHelper { const ACTION_CONTINUE = 1001; public static function orderSuccess($spab4bee, callable $spd1dffc) { $spd09b83 = null; try { return DB::transaction(function () use($spab4bee, &$spd09b83, $spd1dffc) { $spd09b83 = \App\Order::where('id', $spab4bee)->lockForUpdate()->firstOrFail(); $sp4e109c = $spd1dffc($spd09b83); if ($sp4e109c !== self::ACTION_CONTINUE) { return $sp4e109c; } $sp7990f7 = User::where('id', $spd09b83->user_id)->lockForUpdate()->firstOrFail(); $sp7990f7->m_all += $spd09b83->income; $sp7990f7->saveOrFail(); $spa3f8d3 = new FundRecord(); $spa3f8d3->user_id = $spd09b83->user_id; $spa3f8d3->type = FundRecord::TYPE_IN; $spa3f8d3->amount = $spd09b83->income; $spa3f8d3->all = $sp7990f7->m_all; $spa3f8d3->frozen = $sp7990f7->m_frozen; $spa3f8d3->paid = $sp7990f7->m_paid; $spa3f8d3->balance = $sp7990f7->m_balance; $spa3f8d3->remark = '订单#' . $spd09b83->order_no; $spa3f8d3->order_id = $spd09b83->id; $spa3f8d3->saveOrFail(); return true; }); } catch (\Throwable $sp2251d3) { $spc5e95b = 'FundHelper.orderSuccess error, order_id:' . $spab4bee; if ($spd09b83) { $spc5e95b .= ', user_id:' . $spd09b83->user_id . ',income:' . $spd09b83->income . ',order_no:' . $spd09b83->order_no; } Log::error($spc5e95b . ' with exception:', array('Exception' => $sp2251d3)); return false; } } public static function orderFreeze($spab4bee, $sp4e9004) { $spd09b83 = null; try { return DB::transaction(function () use($spab4bee, &$spd09b83, $sp4e9004) { $spd09b83 = \App\Order::where('id', $spab4bee)->lockForUpdate()->firstOrFail(); if ($spd09b83->status === Order::STATUS_REFUND) { return false; } if ($spd09b83->status === Order::STATUS_FROZEN) { return true; } $sp37e56d = $spd09b83->status; if ($sp37e56d === \App\Order::STATUS_SUCCESS) { $spacdeb0 = '已发货'; } elseif ($sp37e56d === \App\Order::STATUS_UNPAY) { $spacdeb0 = '未付款'; } elseif ($sp37e56d === \App\Order::STATUS_PAID) { $spacdeb0 = '未发货'; } else { throw new \Exception('unknown'); } $sp7990f7 = User::where('id', $spd09b83->user_id)->lockForUpdate()->firstOrFail(); $spa3f8d3 = new FundRecord(); $spa3f8d3->user_id = $spd09b83->user_id; $spa3f8d3->type = FundRecord::TYPE_OUT; $spa3f8d3->order_id = $spd09b83->id; $spa3f8d3->remark = $spd09b83 === $spd09b83 ? '' : '关联订单#' . $spd09b83->order_no . ': '; if ($sp37e56d === \App\Order::STATUS_SUCCESS) { $sp7990f7->m_frozen += $spd09b83->income; $sp7990f7->saveOrFail(); $spa3f8d3->amount = -$spd09b83->income; $spa3f8d3->remark .= $sp4e9004 . ', 冻结订单#' . $spd09b83->order_no; } else { $spa3f8d3->amount = 0; $spa3f8d3->remark .= $sp4e9004 . ', 冻结订单(' . $spacdeb0 . ')#' . $spd09b83->order_no; } $spa3f8d3->all = $sp7990f7->m_all; $spa3f8d3->frozen = $sp7990f7->m_frozen; $spa3f8d3->paid = $sp7990f7->m_paid; $spa3f8d3->balance = $sp7990f7->m_balance; $spa3f8d3->saveOrFail(); $spd09b83->status = \App\Order::STATUS_FROZEN; $spd09b83->frozen_reason = ($spd09b83 === $spd09b83 ? '' : '关联订单#' . $spd09b83->order_no . ': ') . $sp4e9004; $spd09b83->saveOrFail(); return true; }); } catch (\Throwable $sp2251d3) { $spc5e95b = 'FundHelper.orderFreeze error'; if ($spd09b83) { $spc5e95b .= ', order_no:' . $spd09b83->order_no . ', user_id:' . $spd09b83->user_id . ', amount:' . $spd09b83->income; } else { $spc5e95b .= ', order_no: null'; } Log::error($spc5e95b . ' with exception:', array('Exception' => $sp2251d3)); return false; } } public static function orderUnfreeze($spab4bee, $spcc3bed, callable $sp3ba85e = null, &$spb5701c = null) { $spd09b83 = null; try { return DB::transaction(function () use($spab4bee, &$spd09b83, $spcc3bed, $sp3ba85e, &$spb5701c) { $spd09b83 = \App\Order::where('id', $spab4bee)->lockForUpdate()->firstOrFail(); if ($sp3ba85e !== null) { $sp4e109c = $sp3ba85e(); if ($sp4e109c !== self::ACTION_CONTINUE) { return $sp4e109c; } } if ($spd09b83->status === Order::STATUS_REFUND) { $spb5701c = $spd09b83->status; return false; } if ($spd09b83->status !== Order::STATUS_FROZEN) { $spb5701c = $spd09b83->status; return true; } $spe67550 = $spd09b83->card_orders()->exists(); if ($spe67550) { $spb5701c = \App\Order::STATUS_SUCCESS; $spacdeb0 = '已发货'; } else { if ($spd09b83->paid_at === NULL) { $spb5701c = \App\Order::STATUS_UNPAY; $spacdeb0 = '未付款'; } else { $spb5701c = \App\Order::STATUS_PAID; $spacdeb0 = '未发货'; } } $sp7990f7 = User::where('id', $spd09b83->user_id)->lockForUpdate()->firstOrFail(); $spa3f8d3 = new FundRecord(); $spa3f8d3->user_id = $spd09b83->user_id; $spa3f8d3->type = FundRecord::TYPE_IN; $spa3f8d3->remark = $spd09b83 === $spd09b83 ? '' : '关联订单#' . $spd09b83->order_no . ': '; $spa3f8d3->order_id = $spd09b83->id; if ($spe67550) { $sp7990f7->m_frozen -= $spd09b83->income; $sp7990f7->saveOrFail(); $spa3f8d3->amount = $spd09b83->income; $spa3f8d3->remark .= $spcc3bed . ', 解冻订单#' . $spd09b83->order_no; } else { $spa3f8d3->amount = 0; $spa3f8d3->remark .= $spcc3bed . ', 解冻订单(' . $spacdeb0 . ')#' . $spd09b83->order_no; } $spa3f8d3->all = $sp7990f7->m_all; $spa3f8d3->frozen = $sp7990f7->m_frozen; $spa3f8d3->paid = $sp7990f7->m_paid; $spa3f8d3->balance = $sp7990f7->m_balance; $spa3f8d3->saveOrFail(); $spd09b83->status = $spb5701c; $spd09b83->saveOrFail(); return true; }); } catch (\Throwable $sp2251d3) { $spc5e95b = 'FundHelper.orderUnfreeze error'; if ($spd09b83) { $spc5e95b .= ', order_no:' . $spd09b83->order_no . ', user_id:' . $spd09b83->user_id . ',amount:' . $spd09b83->income; } else { $spc5e95b .= ', order_no: null'; } Log::error($spc5e95b . ' with exception:', array('Exception' => $sp2251d3)); return false; } } }