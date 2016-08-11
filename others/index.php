<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>workerman-chat PHP聊天室 Websocket(HTLM5/Flash)+PHP多进程socket实时推送技术</title>
<script type="text/javascript">
//WebSocket = null;
</script>
<!--link href="/css/bootstrap.min.css" rel="stylesheet">
<link href="/css/style.css" rel="stylesheet"-->
<!--link href="/css/chat.css" rel="stylesheet"i-->
<!-- Include these three JS files: -->
<script type="text/javascript" src="/js/swfobject.js"></script>
<script type="text/javascript" src="/js/web_socket.js"></script>
<script type="text/javascript" src="/js/json.js"></script>
<script type="text/javascript" src="/js/jquery.min.js"></script>
<style>
.speech_item{
vertical-align:top;
font-family:SimSun;
font-size:16px;
letter-spacing:0px;
line-height:180%;
}

.speech_user{
color:#01A5BD;
display:inline-block
}


.speech_content{
color:#D0D0D0;
display:inline-block;
vertical-align:top;
}

.speech_warning{
color:#FBDB60;
display:inline-block;
vertical-align:top;
font-family:SimHei;
}

.room_item{
}

.room_user{
color:#01A5BD;
display:inline-block
}

.room_user_choose{
color:#01A5BD;
display:inline-block;
cursor:pointer;
}

.room_user_full{
color:#FBDB60;
display:inline-block
}

.user_list_item
{
color:#01A5BD;
}

img{
display:inline-block;
vertical-align:top;
cursor:pointer;
}
</style>

<script type="text/javascript">
if (typeof console == "undefined") {    this.console = { log: function (msg) {  } };}
WEB_SOCKET_SWF_LOCATION = "/swf/WebSocketMain.swf";
WEB_SOCKET_DEBUG = true;
var ws, name, client_list={},timeid, reconnect=false, room, game, join, language;
function init() {
    // 创建websocket
    ws = new WebSocket("ws://121.40.196.168:7272");
    room = '<?php echo isset($_GET['room']) ? $_GET['room'] : NULL?>';
    name = '<?php echo isset($_GET['name']) ? $_GET['name'] : NULL?>';
    game = '<?php echo isset($_GET['game']) ? $_GET['game'] : NULL?>';
    join = '<?php echo isset($_GET['join']) ? $_GET['join'] : NULL?>';
    language = '<?php echo isset($_GET['language']) ? $_GET['language'] : "zh"?>';
    // 当socket连接打开时，输入用户名
    ws.onopen = function() {
        timeid && window.clearInterval(timeid);
        //	  room = '<?php echo isset($_GET['room']) ? $_GET['room'] : NULL ?>';
        //	  name = '<?php echo isset($_GET['name']) ? $_GET['name'] : NULL?>';
        if(name == false)
        {
            if(language.indexOf("zh") === 0)
                warning("请先登陆");
            else
                warning('please login');
            callPC("log error name_empty");
            return;
        }
        if(game == false)
        {
            if(language.indexOf("zh") === 0)
                warning("请选择游戏");
            else
                warning("please select a game");
            callPC("log error game_empty");
            return;
        }
        if(join == 'directly' && room == false)
        {
            if(language.indexOf("zh") === 0)
                warning("请选择房间");
            else
                warning("please select a room");
            callPC("log error join_room_empty");
            return;
        }
        if(reconnect == false)
        {
            // 登录
            var login_data;
            if(join == 'directly')
            {
                login_data = JSON.stringify({"type":"login", "join": true, "name":name,"game":game, "room":room});    
            }
            else
            {
                login_data = JSON.stringify({"type":"login","name":name,"game":game});
            }
            console.log("websocket握手成功，发送登录数据:"+login_data);
            ws.send(login_data);
            reconnect = true;
            callPC("log notice handshake_success");
        }
        else
        {
            // 断线重连
            var relogin_data;
            if(join == 'directly')
            {
                relogin_data = JSON.stringify({"type":"login", "join": true, "name":name,"game":game, "room":room});    
            }
            else
            {
                relogin_data = JSON.stringify({"type":"login","name":name,"game":game});
            }
            console.log("websocket握手成功，发送重连数据:"+relogin_data);
            ws.send(relogin_data);
            callPC("log notice handshake_reconnect_success");
        }
    };
    // 当有消息时根据消息类型显示不同信息
    ws.onmessage = function(e) {
        console.log(e.data);
        var data = JSON.parse(e.data);
        switch(data['type']){
            // 服务端ping客户端
            case 'ping':
                ws.send(JSON.stringify({"type":"pong"}));
                break;
                // 登录 更新用户列表
            case 'login':
                if(data.hasOwnProperty('msg_list'))
                {
                    flush_msg_list(data['msg_list']);
                }
                if(language.indexOf("zh") === 0)
                    warning( "加入房间成功! 开始聊天吧~");
                else
                    warning( "join chat successfully, have fun");

                console.log(data['name']+"登录成功");
                callPC("log notice login_success");
                break;
                // 断线重连，只更新用户列表
            case 're_login':
                //{"type":"re_login","client_id":xxx,"client_name":"xxx","client_list":"[...]","time":"xxx"}
                //if(name == data['name'])
                //{
                if(data.hasOwnProperty('msg_list'))
                {
                    flush_msg_list(data['msg_list']);
                }
                if(language.indexOf("zh") === 0)
                    warning("断线重连成功!");
                else
                    warning("reconnecting successful");

                console.log(data['name']+"登录成功");
                console.log(data['name']+"重连成功");
                callPC("log notice relogin_success");
                break;
                // 发言
            case 'say':
                //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
                say(data['fname'], data['content'], data['time']);
                callPC("new_message"); 
                break;
            case "unroom":
                if(language.indexOf("zh") === 0)
                    warning("请先选择一个房间吧~");
                else
                    warning("please select a room first");
                callPC("log warning msg_send_noroom");
                break;
            case "ungame":
                if(language.indexOf("zh") === 0)
                    warning("该游戏暂不支持聊天");
                else
                    warning("please select a room first");
                callPC("log warning msg_send_noroom");
                break;
            case "unsay":
                //if(data['name'] == name)
                if(language.indexOf("zh") === 0)
                    warning(data['content'] + " 消息发送失败");
                else
                    warning(data['content'] + " message sending error");
                callPC("log warning msg_send_error");
                break;
                // 用户退出 更新用户列表
            case 'logout':
                //{"type":"logout","client_id":xxx,"time":"xxx"}
                //say(data['fname'], data['fname']+' 退出了', data['time']);
                //$("#"+data['fid']).remove();
                // flush_client_list(data['client_list']);
                break;
            case 'login_fail':
                //if(data['name'] == name)
                if(language.indexOf("zh") === 0)
                    warning("该游戏暂不支持聊天(~^~)!");
                else
                    warning("sorry, this game does not support chat");
                callPC("log error game_support_error");
                break;
            case 'rooms':
                // flush_room_list(data['list']);
                if(language.indexOf("zh") === 0)
                    warning("请先选择一个房间吧~");
                else
                    warning("please select a room first");
                callPC("multi_room " + game);
                break;
        }
        };
        ws.onclose = function() {
            if(language.indexOf("zh") === 0)
                warning("连接断开，请重试");
            else
                warning("Disconnect, please try again");
            console.log("连接关闭，定时重连");
            callPC("log warning connect_close");
            // 定时重连, Qweb 失效
            //window.clearInterval(timeid);
            //timeid = window.setInterval(init, 3000);
        };
        ws.onerror = function() {
            console.log("出现错误");
        };
    }


    // 刷新用户列表框
    function flush_client_list(client_list){
        var userlist_window = $("#userlist");
        var client_list_slelect = $("#client_list");
        userlist_window.empty();
        client_list_slelect.empty();
        userlist_window.append('<h4>在线用户</h4><ul>');
        client_list_slelect.append('<option value="all" id="cli_all">所有人</option>');
        for(var p in client_list){
            userlist_window.append('<li id="'+client_list[p]['id']+'">'+client_list[p]['name']+'</li>');
            client_list_slelect.append('<option value="'+client_list[p]['client_id']+'">'+client_list[p]['client_name']+'</option>');
        }
        $("#client_list").val(select_client_id);
        userlist_window.append('</ul>');
    }

    function flush_msg_list(msg_list)
    {
	document.getElementById("dialog").innerHTML = "";
        for(var i in msg_list)
        {
            say( msg_list[i]['fname'], msg_list[i]['content'], msg_list[i]['time']);
        }
    }

    function join_in_room(child_id)
    {
        var msg = JSON.stringify({"type":"login", "1": true, "name":name,"room":room+"_"+child_id});    
        ws.send(msg);
    }


    // 发言
    function say(from_client_name, content, time){
	var length = document.getElementsByClassName("speech_item").length;
        if(length > 100)
        {
		var last = document.getElementsByClassName("speech_item")[0].parentNode;
		document.getElementById("dialog").removeChild(last);
       }

        content = content.replace(/#pic#(.*)#\/pic#/mg, "<img onload='picLoaded();' onclick='clickPic(\"$1\")' src='http://server.droid4x.cn/$1@!test2'></img>");
	var dialog = document.getElementById("dialog");
	var newElement = document.createElement('div'); 
	newElement.innerHTML = '<div class="speech_item"><div class="speech_user">'+from_client_name+'： </div><div class="speech_content">' +content+ '</div>';
	dialog.appendChild(newElement); 
        //alert('<div class="speech_item"><div style="color:#00F">'+from_client_name+' : '+content+'</div></div>');
        window.scrollTo(0,document.body.scrollHeight);
        callPC("sizechanged " + document.body.scrollWidth + " " + document.body.scrollHeight)
    }

    function picLoaded()
    {
        window.scrollTo(0,document.body.scrollHeight);
        callPC("sizechanged " + document.body.scrollWidth + " " + document.body.scrollHeight)
    }

    function clickPic(pic)
    {
        callPC("click_pic http://server.droid4x.cn/" + pic);
    }

    function onSubmitNew(input)
    {
        //   var to_client_id = $("#client_list option:selected").attr("value");
        //  var to_client_name = $("#client_list option:selected").text();
        var to_client_id = 'all';
        var to_client_name = 'all';
        ws.send(JSON.stringify({"type":"say","tid":to_client_id,"tname":to_client_name,"content":input}));
    }	

    function warning(content)
    {
	var length = document.getElementsByClassName("speech_item").length;
        if(length > 100)
        {
		var last = document.getElementsByClassName("speech_item")[0].parentNode;
		document.getElementById("dialog").removeChild(last);
        }
        var pic = "";
        if(language.indexOf("zh") === 0)
            pic = "图片";
        else
            pic = "picture";

        content = content.replace(/#pic#(.*)#\/pic#/mg, pic);

	var dialog = document.getElementById("dialog");
	var newElement = document.createElement('div'); 
	newElement.innerHTML = '<div class="speech_item"><div class="speech_warning">' +content+ '</div>';
	dialog.appendChild(newElement); 
       // dialog.append('<div>' +content+ '</div>');
        window.scrollTo(0,document.body.scrollHeight);
        callPC("sizechanged " + document.body.scrollWidth + " " + document.body.scrollHeight)
    }

    function callPC(msg)
    {
        if(typeof(chatinfo_window) !== "undefined")
            chatinfo_window.jscall(msg);
        else
            return;
    }


    function randomString(len) {
        　　len = len || 32;
        　　var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
        　　var maxPos = $chars.length;
        　　var pwd = '';
        　　for (i = 0; i < len; i++) {
            　　　　pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
            　　}
        　　return pwd;
    }


    function sendPing()
    {
        ws.send(JSON.stringify({"type":"pong"}));
    }

    $(function(){
            select_client_id = 'all';
            $("#client_list").change(function(){
                select_client_id = $("#client_list option:selected").attr("value");
                });
            });
    </script>
        </head>
        <body onload="init();" oncontextmenu=self.event.returnValue=false>
        <div class="container">
        <div class="row clearfix">
        <div class="col-md-6 column">
        <div class="thumbnail">
        <div class="row" id='room_list'>
        </div>
        <div class="caption" id="dialog" style='overflow-x:hidden;word-wrap: break-word; word-break: break-all;'></div>
        </div>
        </div>
        </body>
        </html>
