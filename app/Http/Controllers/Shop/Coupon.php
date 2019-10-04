<?php
namespace App\Http\Controllers\Shop; use App\Category; use App\Product; use App\Library\Response; use Carbon\Carbon; use Illuminate\Http\Request; use App\Http\Controllers\Controller; class Coupon extends Controller { function info(Request $spaa0004) { $spa990f1 = (int) $spaa0004->post('category_id', -1); $sp52bbb2 = (int) $spaa0004->post('product_id', -1); $sp36c7e2 = $spaa0004->post('coupon'); if (!$sp36c7e2) { return Response::fail('请输入优惠券'); } if ($spa990f1 > 0) { $sp9c1285 = Category::findOrFail($spa990f1); $sp71c904 = $sp9c1285->user_id; } elseif ($sp52bbb2 > 0) { $sp59cabc = Product::findOrFail($sp52bbb2); $sp71c904 = $sp59cabc->user_id; } else { return Response::fail('请先选择分类或商品'); } $spb5f71e = \App\Coupon::where('user_id', $sp71c904)->where('coupon', $sp36c7e2)->where('expire_at', '>', Carbon::now())->whereRaw('`count_used`<`count_all`')->get(); foreach ($spb5f71e as $sp36c7e2) { if ($sp36c7e2->category_id === -1 || $sp36c7e2->category_id === $spa990f1 && ($sp36c7e2->product_id === -1 || $sp36c7e2->product_id === $sp52bbb2)) { $sp36c7e2->setVisible(array('discount_type', 'discount_val')); return Response::success($sp36c7e2); } } return Response::fail('您输入的优惠券信息无效<br>如果没有优惠券请不要填写'); } }