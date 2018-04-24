<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use Session;

class MainController extends Controller
{
     protected $heads = [
	     "NickName" => "昵称",
	     "City"    => "城市",
	     "RemarkName" => "备注",
	     "Sex" => "性别",
	     "Signature" => "签名",

     ];
     //获取二维码的路径，并初始化session
     public function init(Request $request){
	     $id = app('session')->get('id');
	     if( empty($id)){
		     $id = time().rand(100, 999);
		     app('session')->put('id', $id);
	     }
	     //Redis::set('current_id', $id);
             file_put_contents("/data/webchat/web/config/id_list", $id);
	     $timeout = 10;
	     while($timeout){
		     if(! Redis::exists($id)){
			     sleep(2);
                 $timeout -= 2;
                continue;
             }
             $info = Redis::get($id);
             $info = json_decode($info, true);
             $qrpath = str_replace('/data/webchat/web/public/', '', $info['qrfile']);
             return $qrpath;
         } 
         abort(500, '系统错误，清联系客服');

     }

     // 获取好友列表
     public function friends(){
         $id = app('session')->get('id'); 
         $id = 'uv';
         $timeout = 20;
         while($timeout){
              $infojson = Redis::get($id);
              file_put_contents('./friends.log', $infojson);
              print_r($infojson);die;
              $info = json_decode($infojson, true);
              print_r($infojson);die;
              if(empty($info['friends'])){
                  sleep(2);  
                  $timeout -= 2;
                  continue;
              }
              $friends = $info['friends'];
              return response()->json($friends);
         }
         abort(500, '好友获取失败');
    
     }
     
     //导出好友列表
     public function export(Request $request){
          $authKey = $request->input('key', '');
         if( ! Redis::sismember('auth_key', $authKey)){
		abort(400, '授权错误');
	}
	 $id = app('session')->get('id');
	 $timeout = 20;
	 while($timeout){
		 $infojson = Redis::get($id);
		 file_put_contents('./friends.log', $infojson);
		 $info = json_decode($infojson, true);
		 if(empty($info['friends'])){
			 sleep(2);
			 $timeout -= 2;
			 continue;
		 }
		 $friends = $info['friends'];
		 break;
		 return response()->json($friends);
	 }
	 if(empty($friends)){
		 abort(500, '好友获取失败');
	 }
	 $this->download_send_headers("{$id}_friend_" . date("Y-m-d") . ".csv");
	 echo $this->array2csv($friends);
         Redis::srem('auth_key', $authKey);
	 die();

     }
     function logout(Request $request){
	$request->session()->flush();
        return response()->json(["status" =>true]);
    }
     function download_send_headers($filename) {
	     // disable caching
	     $now = gmdate("D, d M Y H:i:s");
	     header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
	     header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
	     header("Last-Modified: {$now} GMT");

	     // force download  
	     header("Content-Type: application/force-download");
	     header("Content-Type: application/octet-stream");
	     header("Content-Type: application/download");

	     // disposition / encoding on response body
	     header("Content-Disposition: attachment;filename={$filename}");
	     header("Content-Transfer-Encoding: binary");
     }
     function array2csv(array &$array)
     {
            $validHeads = array_keys($this->heads);
            foreach($array as $index => $items){
		foreach($items as $key => $item){
                        
			if(! in_array($key,$validHeads)){
				unset($array[$index][$key]);
			}
			
		}
		}
	     if (count($array) == 0) {
		     return null;
	     }
	     ob_start();
              
	     $df = fopen("php://output", 'w');
		fprintf($df, chr(0xEF).chr(0xBB).chr(0xBF));
             $heads =  array_keys(reset($array));
            $headNames = [];
             foreach($heads as $head){
		$headNames []= $this->heads[$head];
}
	     fputcsv($df, $headNames);
	     foreach ($array as $row) {
                      foreach($row as $index => $value){
                                 if($index == 'Sex'){
                                     switch($value){
					case 0: $row[$index] = "未知";break;
					case 1: $row[$index] = "男";break;
					case 2: $row[$index] = "女";break;
}
                                 }
				if(is_array($value)){
					$row[$index] = implode(',', $value);
			}
			}
		     fputcsv($df, $row);
	     }
	     return ob_get_clean();
     }
      //生成key
     public function generate(Request $request){
            $name = $request->input('name', '');
            if($name != 'banliyun'){
			abort(400, '授权错误');
		}
            
            $key  = $this->generateRandomString(6);
            Redis::sadd('auth_key', $key);
            echo $key; 
        
    }
    public function check(Request $request){
       $key = $request->input('key', '');

       return response()->json(['result' => (bool) Redis::sismember('auth_key', $key)]);
    }
    public function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

}
