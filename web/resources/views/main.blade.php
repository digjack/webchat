<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>板栗工具台</title>
	<link href="https://cdn.bootcss.com/bootstrap/4.1.0/css/bootstrap.min.css" rel="stylesheet">

	<style>  
        .qrstyle{
	margin:0 50%;
}
        .buttonstyle{
	margin:0 30%;
}
 .input-group{
	margin-left: 320px;
    margin-bottom: 30px;
}
  .help{

    position: absolute;
    top: 200px;
    right: 100px;

}
 .buttonstyle > button{
    margin-left: 50px
}
</style>  
</head>
    <body class="container">
 
    <div  class="col-lg-6 ">
	<img id="qr_image" class="qrstyle" src=""> </img>
    </div>
    <div class="input-group col-md-4">
  <input type="text" class="form-control" id="key" placeholder="输入key..." aria-label="输入key.." aria-describedby="basic-addon2">
  <div class="input-group-append">
    <button id="key-sure" class="btn btn-outline-secondary" type="button" onclick="check()">校验</button>
  </div>
</div>
    <div class="col-md-4 buttonstyle">
        <a  id="export" class="btn btn-primary btn-lg" target="_blank" href="/export">导出列表</a>
        <button class="btn btn-primary btn-lg" onclick="refreshQr()">刷新二维码</button>
    </div>
    <div class="help panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">操作步骤</h3>
  </div>
  <div class="panel-body">
    1. 用微信扫描二维码，在手机上点击登录 <Br>
    2. 输入key, 点击校验, 校验成功后，关闭校验弹窗 <Br>
    3. 大约5秒后，点击导出列表 <Br>
    注： 导出列表后key就会失效。
  </div>
</div>
    </body>
<script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/4.1.0/js/bootstrap.min.js"></script>
<script>
$( document ).ready(function() {
    var key;
    pathOject = $.ajax({url:"/qr", async:false});
    $("#qr_image").attr("src",pathOject.responseText);
});
function exportFriends()
{
   key = $("#key").val();
    $.ajax({url:"/export?key=".concat(key),async:false});
}
function refreshQr()
{
    $.ajax({url:"/logout",async:false});
    location.reload();
}
function check(){
    key = $("#key").val();
    $.ajax({url: "/check?key=".concat(key), async:false, success: function(data){
                if(data.result === true){
                    alert('校验成功, key将在导出列表后失效!');
    $("#export").attr('href',  "/export?key=".concat(key));
                }else{
                    alert('校验失败');
                }
             
	}});
}
	
</script>
</html>
