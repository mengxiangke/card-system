<?php
namespace App\Http\Controllers; use App\System; use Illuminate\Foundation\Bus\DispatchesJobs; use Illuminate\Routing\Controller as BaseController; use Illuminate\Foundation\Validation\ValidatesRequests; use Illuminate\Foundation\Auth\Access\AuthorizesRequests; use Illuminate\Http\Request; class Controller extends BaseController { use AuthorizesRequests, DispatchesJobs, ValidatesRequests; function authQuery(Request $spaa0004, $spc933c6, $sp48e2b4 = 'user_id', $sp2aa27d = 'user_id') { return $spc933c6::where($sp48e2b4, \Auth::id()); } protected function getUserId(Request $spaa0004, $sp2aa27d = 'user_id') { return \Auth::id(); } protected function getUserIdOrFail(Request $spaa0004, $sp2aa27d = 'user_id') { $sp71c904 = self::getUserId($spaa0004, $sp2aa27d); if ($sp71c904) { return $sp71c904; } else { throw new \Exception('参数缺少 ' . $sp2aa27d); } } protected function getUser(Request $spaa0004) { return \Auth::getUser(); } protected function checkIsInMaintain() { if ((int) System::_get('maintain') === 1) { $sp32fcf1 = System::_get('maintain_info'); echo view('message', array('title' => '维护中', 'message' => $sp32fcf1)); die; } } protected function msg($sp6ca805, $spff5bce = null, $sp168997 = null) { return view('message', array('message' => $sp6ca805, 'title' => $spff5bce, 'exception' => $sp168997)); } }