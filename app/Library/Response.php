<?php
namespace App\Library; class Response { public static function json($spa480b6 = array(), $sp8d8a6c = 200, array $sp234b3d = array(), $sp61ed73 = 0) { return response()->json($spa480b6, $sp8d8a6c, $sp234b3d, $sp61ed73); } public static function success($spa480b6 = array()) { return self::json(array('message' => 'success', 'data' => $spa480b6)); } public static function fail($spc5e95b = 'fail', $spa480b6 = array()) { return self::json(array('message' => $spc5e95b, 'data' => $spa480b6), 500); } public static function forbidden($spc5e95b = 'forbidden', $spa480b6 = array()) { return self::json(array('message' => $spc5e95b, 'data' => $spa480b6), 403); } }