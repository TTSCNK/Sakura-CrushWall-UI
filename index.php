<!-- 樱花表白墙制作人: 樱振宇 UI重构:TTS-->
<?php
// ==========================================
//  后端逻辑保留区 (严禁修改)
// ==========================================
$ip=$_SERVER["REMOTE_ADDR"];
// 获取IP拦截列表
$fjip_content = @file_get_contents("api/yzy/txt/fjip"); 
if($fjip_content){
    $fjdip=explode("|", $fjip_content);
    foreach($fjdip as $qdgip){
        if($ip==$qdgip){
        die("<script>window.location.replace('http://api.sakura.gold/');</script>");
        }
    }
}

include "sakura/mysql.php";
// 设置网站数据
$sql = "select * from yingzhenyu";
$sakura = $Mysql->query($sql);
$szwz= $sakura->fetch_all()[0];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title><?php echo $szwz[0]; ?></title>
    <link rel="icon" href="img/yingzhenyu.ico" />
    
    <!-- 引入 UI 框架 Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- 引入 FontAwesome 图标库 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- 引入字体 -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- 微软统计 (保留原版) -->
    <script type="text/javascript">
        (function(c,l,a,r,i,t,y){
            c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
            t=l.createElement(r);t.async=1;t.src="https://www.clarity.ms/tag/"+i;
            y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
        })(window, document, "clarity", "script", "hf4jw4u6jt");
    </script>

    <style>
        /* ==========================================
           全局样式与特效
           ========================================== */
        body {
            font-family: 'Noto Sans SC', sans-serif;
            background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
            background-color: #ffdee9;
            background-image: linear-gradient(0deg, #ffdee9 0%, #b5fffc 100%);
            min-height: 100vh;
            color: #475569;
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
        }

        /* 隐藏滚动条 */
        ::-webkit-scrollbar { display: none; }
        
        /* 樱花背景容器 */
        .sakura-bg { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; pointer-events: none; }
        .petal { position: absolute; background-color: #ffc0cb; border-radius: 150% 0 150% 0; animation: fall infinite linear; }
        @keyframes fall {
            0% { transform: translate(0, -10vh) rotate(0deg); opacity: 1; }
            100% { transform: translate(100px, 110vh) rotate(360deg); opacity: 0; }
        }

        /* 毛玻璃卡片 */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        /* ==========================================
           核心兼容层：覆盖 API 返回的旧 HTML 样式
           ========================================== */
        
        /* 表白内容卡片 */
        .lo_ve {
            background: rgba(255, 255, 255, 0.85) !important;
            border-radius: 16px !important;
            padding: 20px !important;
            margin-bottom: 20px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            border: 1px solid rgba(255,255,255,0.9) !important;
            transition: transform 0.2s;
            position: relative;
        }
        .lo_ve:hover { transform: translateY(-2px); }

        /* 顶部标题 (Ta的名字) */
        .love_zero_ta, .love_two_ta {
            font-weight: 700;
            color: #db2777; /* pink-600 */
            margin-bottom: 8px;
            font-size: 1.05rem;
            display: flex;
            align-items: center;
            background: transparent !important;
            padding: 0 !important;
        }
        .love_zero_ta::before {
            content: ''; display: inline-block; width: 6px; height: 16px;
            background: #db2777; margin-right: 10px; border-radius: 4px;
        }

        /* 正文内容 */
        .love_zero_xx {
            color: #334155;
            font-size: 0.95rem;
            line-height: 1.7;
            margin-bottom: 12px;
            word-wrap: break-word;
            padding: 0 !important;
        }
        
        /* 底部信息 */
        .love_zero_bz {
            font-size: 0.75rem;
            color: #94a3b8;
            text-align: right;
            padding-top: 8px;
            width: 100%;
        }

        /* 展开全文链接 */
        .zknr { color: #3b82f6; cursor: pointer; font-size: 0.85rem; margin-left: 5px; font-weight: bold; }

        /* 图片处理 */
        .love_two_img {
            width: 100%; height: 200px; object-fit: cover; border-radius: 12px; margin: 10px 0;
            border: 1px solid rgba(0,0,0,0.05);
        }

        /* 隐藏旧元素 */
        .bbq_bt, .bt_z_tx, .bt_y_gd, .logo { display: none !important; }

        /* ==========================================
           互动区 UI 重构 (点赞 & 评论)
           ========================================== */
        
        /* 底部操作栏 */
        .love_actions {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 12px 0 0 0 !important;
            border-top: 1px solid #f1f5f9 !important;
            margin-top: 10px !important;
        }

        /* 按钮通用 */
        .like_button, .comment_button {
            background: transparent !important; border: none !important;
            font-size: 0.9rem !important; cursor: pointer !important;
            display: flex !important; align-items: center !important;
            transition: all 0.2s !important; color: #64748b !important;
            font-weight: 500 !important; padding: 6px 12px !important;
            border-radius: 8px !important;
        }
        .like_button:hover, .comment_button:hover { background-color: #f8fafc !important; }
        .like_button i, .comment_button i { margin-right: 6px; font-size: 1.1rem; }

        /* 点赞激活 */
        .like_button.liked { color: #ec4899 !important; }
        .like_button.liked i { animation: heartBeat 0.6s; }

        /* 评论区容器 */
        .comments_area {
            background-color: #f8fafc !important; padding: 15px !important;
            border-radius: 12px !important; margin-top: 15px !important;
            animation: slideDown 0.3s ease-out; display: none;
        }

        /* 评论列表 */
        .comments_list { margin-bottom: 15px !important; max-height: 300px; overflow-y: auto; }

        /* 单条评论 */
        .comment_item {
            background: white !important; padding: 12px !important;
            border-radius: 10px !important; margin-bottom: 10px !important;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03) !important;
            border: 1px solid #f1f5f9 !important;
        }
        .comment_username { color: #db2777 !important; font-weight: 700 !important; font-size: 0.85rem !important; margin-bottom: 4px !important; }
        .comment_content { font-size: 0.9rem !important; color: #334155 !important; }
        .comment_meta { font-size: 0.7rem !important; color: #94a3b8 !important; text-align: right !important; margin-top: 5px !important; }

        /* 评论输入 */
        .comment_name, .comment_textarea {
            width: 100% !important; padding: 10px !important; margin-bottom: 8px !important;
            border: 1px solid #e2e8f0 !important; border-radius: 8px !important;
            font-size: 0.9rem !important; background: white !important;
            transition: all 0.2s !important; outline: none !important;
        }
        .comment_textarea { min-height: 80px !important; resize: none !important; font-family: inherit !important; }
        .comment_name:focus, .comment_textarea:focus { border-color: #f472b6 !important; box-shadow: 0 0 0 3px rgba(244,114,182,0.1) !important; }

        .submit_comment {
            float: right !important; background: linear-gradient(to right, #ec4899, #f43f5e) !important;
            color: white !important; border: none !important; padding: 8px 24px !important;
            border-radius: 99px !important; font-size: 0.85rem !important; font-weight: bold !important;
            cursor: pointer !important; box-shadow: 0 4px 6px rgba(236, 72, 153, 0.25) !important;
            transition: all 0.2s !important;
        }
        .submit_comment:active { transform: scale(0.95); }

        /* 翻页 */
        .fanye { display: flex !important; justify-content: center !important; gap: 10px !important; margin-top: 30px !important; }
        .xyy, .syy, .fanye_sz {
            display: flex; align-items: center; justify-content: center;
            padding: 8px 16px; background: rgba(255,255,255,0.9) !important;
            border-radius: 99px !important; font-size: 0.85rem !important;
            color: #db2777 !important; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            cursor: pointer; font-weight: 600 !important; width: auto !important; float: none !important; border: none !important;
        }
        .fanye_sz:hover, .xyy:active, .syy:active { background: #db2777 !important; color: white !important; }

        /* 动画与提示 */
        @keyframes heartBeat { 0% { transform: scale(1); } 50% { transform: scale(1.3); } 100% { transform: scale(1); } }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        
        #toast-container { position: fixed; top: 90px; left: 50%; transform: translateX(-50%); z-index: 9999; pointer-events: none; }
        .toast-msg {
            background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px);
            padding: 10px 24px; border-radius: 50px; box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            color: #333; font-size: 0.9rem; margin-bottom: 10px; display: flex; align-items: center;
            animation: toastFadeIn 0.3s forwards; border: 1px solid #ffe4e6;
        }
        .toast-msg i { margin-right: 8px; font-size: 1.1rem; }
        .toast-msg.success i { color: #10b981; } .toast-msg.error i { color: #f43f5e; }
        @keyframes toastFadeIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head> 
<body class="flex flex-col items-center p-4 pb-20">

    <!-- 樱花背景 -->
    <div id="sakura-container" class="sakura-bg"></div>
    <!-- Toast 容器 -->
    <div id="toast-container"></div>

    <!-- 顶部悬浮导航 -->
    <nav class="fixed top-0 left-0 w-full z-40 glass px-4 py-3 flex justify-between items-center shadow-sm">
        <div class="flex items-center space-x-2" onclick="jhgg(1);">
            <img src="http://q1.qlogo.cn/g?b=qq&nk=<?php echo $szwz[2]; ?>&s=640" class="w-9 h-9 rounded-full border-2 border-pink-200 cursor-pointer hover:rotate-12 transition shadow-sm">
            <span class="font-bold text-pink-600 text-lg tracking-wide">建陵表白墙</span> <!-- 这里修改你的网站名-->
        </div>
        <div onclick="window.location.href='gdxx.html';" class="bg-gradient-to-r from-pink-500 to-rose-500 text-white px-5 py-1.5 rounded-full text-sm font-bold shadow-lg active:scale-95 transition cursor-pointer flex items-center">
            <i class="fa-solid fa-feather-pointed mr-2"></i> 去表白
        </div>
    </nav>

    <!-- 占位符 -->
    <div class="h-16 w-full"></div>

    <!-- 主内容容器 -->
    <div class="w-full max-w-md space-y-5">

        <!-- 顶部公告卡片 -->
        <div class="relative w-full h-40 rounded-2xl overflow-hidden shadow-lg group cursor-pointer" onclick="window.location.href='gdxx.html';">
            <img src="<?php echo $szwz[5]; ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
            <div class="absolute bottom-4 left-5 text-white">
                <h3 class="font-bold text-lg drop-shadow-md"><?php echo $szwz[0]; ?></h3>
                <p class="text-xs opacity-90"><i class="fa-solid fa-bullhorn mr-1"></i> 点击这里发布你的故事</p>
            </div>
        </div>

        <!-- 滚动通知 -->
        <div class="glass rounded-xl px-4 py-3 flex items-center space-x-3 text-sm text-pink-600 border border-white/50">
            <i class="fa-solid fa-bell animate-bounce text-pink-400"></i>
            <marquee class="flex-1 font-medium" onclick="showToast(this.innerText, 'success');"><?php echo $szwz[7]; ?></marquee>
        </div>

        <!-- 分类切换 Tabs -->
        <div class="glass p-1.5 rounded-xl flex justify-between items-center text-sm font-medium text-gray-500">
            <div id="tab-0" class="tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer transition bg-white text-pink-500 shadow-sm" onclick="love_xzk(0, this);">
                <i class="fa-solid fa-magnifying-glass mb-1 block text-sm"></i> 搜索
            </div>
            <div id="tab-1" class="tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer hover:bg-white/50 transition" onclick="love_xzk(1, this);">
                <i class="fa-solid fa-heart mb-1 block text-sm"></i> 表白
            </div>
            <div id="tab-2" class="tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer hover:bg-white/50 transition" onclick="love_xzk(2, this);">
                <i class="fa-solid fa-mug-hot mb-1 block text-sm"></i> 日常
            </div>
            <div id="tab-3" class="tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer hover:bg-white/50 transition" onclick="love_xzk(3, this);">
                <i class="fa-solid fa-user-group mb-1 block text-sm"></i> 扩列
            </div>
        </div>

        <!-- 搜索功能 (默认隐藏) -->
        <div id="search-box" class="hidden glass p-5 rounded-2xl space-y-4 animate-fade-in border border-white">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-3.5 text-pink-300"></i>
                <input type="text" class="ss_bjk w-full pl-10 pr-4 py-3 bg-white border border-pink-100 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-300 text-sm shadow-sm" placeholder="输入关键词查找TA...">
            </div>
            <div class="flex justify-around text-xs text-gray-600 px-2">
                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-white/50"><input type="radio" name="ss_lx" class="ss_dxk accent-pink-500" checked> <span>表白</span></label>
                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-white/50"><input type="radio" name="ss_lx" class="ss_dxk accent-pink-500"> <span>日常</span></label>
                <label class="flex items-center space-x-2 cursor-pointer p-2 rounded hover:bg-white/50"><input type="radio" name="ss_lx" class="ss_dxk accent-pink-500"> <span>扩列</span></label>
            </div>
            <button onclick="ksss()" class="w-full bg-gradient-to-r from-pink-400 to-rose-400 text-white py-3 rounded-xl font-bold shadow-md active:scale-95 transition">开始搜索</button>
        </div>

        <!-- 置顶内容容器 -->
        <div id="zd_love_container" class="space-y-4">
            <?php
            if (file_get_contents("api/yzy/txt/zd")!="") {
                $zd_love=str_replace("|",",",file_get_contents("api/yzy/txt/zd"));
                $sql = "SELECT * FROM love WHERE FIND_IN_SET(id,\"$zd_love\") ORDER BY FIND_IN_SET(id,\"$zd_love\") ;";
                $sakura = $Mysql->query($sql);
                $my_sj= $sakura->fetch_all();
                foreach($my_sj as $fhz){
                    $bbnr=htmlspecialchars($fhz[3]);
                    $bb_nr=mb_substr($bbnr,0,99,"utf-8");
                    // 强制包裹样式，并增加置顶光环
                    echo '<div class="lo_ve ring-2 ring-pink-300 ring-offset-2 ring-offset-transparent">'; 
                    echo '<div class="love_zero_ta"><i class="fa-solid fa-thumbtack mr-2 animate-bounce"></i> 官方置顶 : '.htmlspecialchars($fhz[2]).'</div>';
                    if(mb_strlen($bbnr,"utf-8")>99){
                        echo '<p class="love_zero_xx"><span onclick="zknr(this)" id="'.$bbnr.'" >'.$bb_nr.'... <span class="zknr">[全文]</span></span></p>';
                    }else{
                        echo '<p class="love_zero_xx">'.$bbnr.'</p>';
                    }
                    echo '<div class="love_zero_bz">笔者: '.htmlspecialchars($fhz[1]).' | '.date("Y-m-d H:i",$fhz[4]).'</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>

        <!-- 动态内容加载区 -->
        <div class="love min-h-[200px]">
            <!-- AJAX 载入内容 -->
        </div>

        <!-- 分页按钮 -->
        <div class="fanye">
            <!-- JS 生成 -->
        </div>

        <!-- 底部版权 -->
        <div class="text-center space-y-2 py-8 opacity-70">
            <span id="sitetime" class="text-xs text-gray-500 block font-mono">加载时间...</span>
            <p class="text-xs text-gray-400">站长是 <?php echo $szwz[2]; ?></p>
        </div>

    </div>

    <!-- 个人中心/公告模态框 -->
    <div class="index_bj fixed inset-0 bg-black/40 backdrop-blur-sm z-50 transition-opacity duration-300 opacity-0 pointer-events-none flex items-center justify-center p-6" onclick="jhgg(0)">
        <div class="index_gg bg-white w-full max-w-sm rounded-3xl shadow-2xl p-6 transform scale-95 transition-all duration-300 relative overflow-hidden" onclick="event.stopPropagation()">
            <!-- 装饰背景 -->
            <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-pink-200 to-rose-200"></div>
            
            <div class="relative text-center mt-8">
                <img src="http://q1.qlogo.cn/g?b=qq&nk=<?php echo $szwz[2]; ?>&s=640" class="w-24 h-24 rounded-full mx-auto border-4 border-white shadow-md bg-white">
                <h3 class="font-bold text-xl text-gray-800 mt-3"><?php echo $szwz[3]; ?></h3>
                <p class="text-xs text-pink-500"><?php echo $szwz[0]; ?></p>
            </div>
            
            <div class="mt-6 space-y-3 text-sm text-gray-600 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                <p class="flex justify-between items-center">
                    <span><i class="fa-brands fa-qq text-blue-400"></i> 站长QQ</span> 
                    <a href="https://tool.gljlw.com/qqq/?qq=<?php echo $szwz[2]; ?>" class="text-blue-500 font-bold bg-blue-50 px-2 py-1 rounded"><?php echo $szwz[2]; ?></a>
                </p>
                <div class="border-t border-gray-200 my-2"></div>
                <p class="text-xs leading-relaxed text-gray-500"><i class="fa-solid fa-quote-left text-pink-300 mr-1"></i><?php echo $szwz[7]; ?></p>
                
                <a href="/" class="block text-center bg-pink-50 text-pink-500 py-3 rounded-xl hover:bg-pink-100 transition font-bold mt-4">
                    <i class="fa-solid fa-cloud-arrow-down mr-1"></i> 下载 APP 客户端
                </a>
            </div>

            <div class="mt-4">
                <audio class="w-full h-8" controls loop src="<?php echo $szwz[4]; ?>"></audio>
            </div>

            <button onclick="jhgg(0);" class="mt-6 w-full bg-gradient-to-r from-pink-400 to-rose-400 text-white py-3 rounded-xl font-bold shadow-lg active:scale-95 transition">朕已知晓</button>
        </div>
    </div>

    <!-- 逻辑脚本 -->
    <script>
        // ==========================================
        //  工具函数：Toast 提示 (替代 alert)
        // ==========================================
        function showToast(message, type = 'success') {
            let container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const icon = type === 'success' ? '<i class="fa-solid fa-circle-check"></i>' : '<i class="fa-solid fa-circle-exclamation"></i>';
            toast.className = `toast-msg ${type}`;
            toast.innerHTML = `${icon} <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // ==========================================
        //  UI 增强：自动美化 API 返回的旧 HTML
        // ==========================================
        function enhanceUI() {
            // 美化点赞按钮
            document.querySelectorAll('.like_button').forEach(btn => {
                if(!btn.querySelector('i')) {
                    let num = (btn.innerText.match(/\d+/) || ['0'])[0];
                    btn.innerHTML = `<i class="fa-regular fa-heart"></i> ${num}`;
                }
            });
            // 美化评论按钮
            document.querySelectorAll('.comment_button').forEach(btn => {
                if(!btn.querySelector('i')) {
                    let num = (btn.innerText.match(/\d+/) || ['0'])[0];
                    btn.innerHTML = `<i class="fa-regular fa-comment-dots"></i> ${num}`;
                }
            });
            // 美化评论输入框
            document.querySelectorAll('.comment_name').forEach(el => el.placeholder = "您的昵称 (必填)");
            document.querySelectorAll('.comment_textarea').forEach(el => el.placeholder = "写下暖心的评论吧...");
            document.querySelectorAll('.submit_comment').forEach(el => el.innerHTML = '<i class="fa-solid fa-paper-plane mr-1"></i> 发送');
        }

        // ==========================================
        //  核心逻辑
        // ==========================================
        
        // 1. 樱花特效
        function createSakura() {
            const container = document.getElementById('sakura-container');
            const petal = document.createElement('div');
            petal.classList.add('petal');
            const size = Math.random() * 10 + 5;
            const left = Math.random() * 100;
            const duration = Math.random() * 5 + 5;
            petal.style.width = `${size}px`; petal.style.height = `${size}px`;
            petal.style.left = `${left}%`; petal.style.animationDuration = `${duration}s`;
            container.appendChild(petal);
            setTimeout(() => petal.remove(), duration * 1000);
        }
        setInterval(createSakura, 600);

        // 2. AJAX 封装
        function ajax(url, successFn, errorFn){
            var xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.send();
            xhr.onreadystatechange = function(){
                if (xhr.readyState == 4) {
                    if (xhr.status == 200) successFn(xhr.responseText);
                    else if(errorFn) errorFn();
                }
            }
        }

        // 3. Tab 切换 (搜索/表白/日常/扩列)
        function love_xzk(typeIndex, btnElement){
            // 样式切换
            document.querySelectorAll('.tab-btn').forEach(el => {
                el.className = 'tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer hover:bg-white/50 transition text-gray-500';
            });
            if(btnElement) btnElement.className = 'tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer transition bg-white text-pink-500 shadow-sm';
            else document.getElementById('tab-' + typeIndex).className = 'tab-btn flex-1 text-center py-2.5 rounded-lg cursor-pointer transition bg-white text-pink-500 shadow-sm';

            var searchBox = document.getElementById('search-box');
            var container = document.querySelector('.love');
            var fanyeDiv = document.querySelector('.fanye');
            var zdContainer = document.getElementById('zd_love_container');

            if(typeIndex === 0){ // 搜索
                searchBox.classList.remove('hidden');
                container.innerHTML = "";
                fanyeDiv.innerHTML = "";
                if(zdContainer) zdContainer.style.display = 'none';
                return;
            }

            // 加载内容
            searchBox.classList.add('hidden');
            var wanMap = { 1: 0, 2: 1, 3: 2 };
            var wan = wanMap[typeIndex];
            
            if(zdContainer) zdContainer.style.display = (wan === 0) ? 'block' : 'none';

            container.innerHTML = '<div class="text-center py-12 text-pink-400"><i class="fa-solid fa-spinner fa-spin text-2xl"></i><p class="mt-2 text-xs">少女祈祷中...</p></div>';

            ajax('../api/cx.php?lx=1&love52=0&wan='+wan, function(res){
                container.innerHTML = res;
                fy('../api/cx.php?lx=0&love52=0&wan='+wan, wan);
                setTimeout(enhanceUI, 100); // 加载完成后美化UI
            }, function(){
                container.innerHTML = '<div class="text-center text-gray-400 py-10">加载失败</div>';
            });
        }

        // 4. 翻页
        function fy(ajax_fy, wan){
            var love_fy = document.querySelector('.fanye');
            ajax(ajax_fy, function(ajax_fhz){
                var json = JSON.parse(ajax_fhz);
                var html = '';
                if(json.dqys > 1) html += `<div class="syy" onclick="ymxz(${json.dqys-2},${wan})"><i class="fa-solid fa-chevron-left mr-1"></i>上页</div>`;
                html += `<div class="fanye_sz" onclick="tzys(${wan})">${json.dqys} / ${json.syys}</div>`;
                if(json.dqys < json.syys) html += `<div class="xyy" onclick="ymxz(${json.dqys},${wan})">下页<i class="fa-solid fa-chevron-right ml-1"></i></div>`;
                love_fy.innerHTML = html;
            }, null);
        }

        function ymxz(yeshu, wan){
             var container = document.querySelector('.love');
             container.innerHTML = '<div class="text-center py-10 text-pink-400"><i class="fa-solid fa-spinner fa-spin"></i> 加载中...</div>';
             ajax('../api/cx.php?lx=1&love52='+yeshu+'&wan='+wan, function(res){
                container.innerHTML = res;
                fy('../api/cx.php?lx=0&love52='+yeshu+'&wan='+wan, wan);
                window.scrollTo({ top: 0, behavior: 'smooth' });
                setTimeout(enhanceUI, 100);
             }, function(){});
        }

        // 5. 交互功能
        function likePost(love_id, button) {
            if (button.classList.contains('processing')) return;
            button.classList.add('processing');
            var xhr = new XMLHttpRequest();
            xhr.open('GET', '../api/like.php?love_id=' + love_id, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    button.classList.remove('processing');
                    if (xhr.status === 200) {
                        try {
                            var res = JSON.parse(xhr.responseText);
                            if (res.status === 1) {
                                button.classList.add('liked');
                                button.innerHTML = `<i class="fa-solid fa-heart"></i> ${res.count}`;
                                showToast("点赞成功 (❤ ω ❤)", "success");
                            } else {
                                showToast(res.msg || "已赞过", "error");
                            }
                        } catch(e) { showToast("服务器错误", "error"); }
                    }
                }
            };
            xhr.send();
        }

        function showComments(love_id) {
            var area = document.getElementById('comments_' + love_id);
            if(!area) return;
            if (area.style.display === 'none' || area.style.display === '') {
                area.style.display = 'block';
                loadComments(love_id);
            } else {
                area.style.display = 'none';
            }
        }

        function loadComments(love_id) {
            var list = document.querySelector('#comments_' + love_id + ' .comments_list');
            if(!list) return;
            list.innerHTML = '<div class="text-center text-gray-400 py-4"><i class="fa-solid fa-spinner fa-spin"></i></div>';
            ajax('../api/get_comments.php?love_id=' + love_id, function(res){
                try {
                    var data = JSON.parse(res);
                    if (data.status === 1 && data.count > 0) {
                        list.innerHTML = '';
                        data.comments.forEach(c => {
                            list.innerHTML += `<div class="comment_item"><div class="comment_username">${c.username||'匿名'}</div><div class="comment_content">${c.content}</div><div class="comment_meta">${c.time}</div></div>`;
                        });
                    } else {
                        list.innerHTML = '<div class="text-center text-gray-400 text-sm py-4">暂无评论，抢沙发~</div>';
                    }
                    enhanceUI();
                } catch(e){ list.innerHTML = '加载出错'; }
            }, null);
        }

        function submitComment(love_id, button) {
            var parent = button.closest('.comment_input') || button.parentNode;
            var txt = parent.querySelector('.comment_textarea').value.trim();
            var name = parent.querySelector('.comment_name').value.trim();
            if(!txt) return showToast('内容不能为空', 'error');
            
            button.disabled = true; button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '../api/comment.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    button.disabled = false; button.innerHTML = '<i class="fa-solid fa-paper-plane mr-1"></i> 发送';
                    if (xhr.status === 200) {
                        var res = JSON.parse(xhr.responseText);
                        if(res.status === 1) {
                            showToast('评论成功 ✨', 'success');
                            parent.querySelector('.comment_textarea').value = '';
                            loadComments(love_id);
                        } else showToast(res.msg, 'error');
                    }
                }
            };
            xhr.send(`love_id=${love_id}&content=${encodeURIComponent(txt)}&username=${encodeURIComponent(name)}`);
        }

        // 6. 其他杂项
        function ksss(){
            var input = document.querySelector(".ss_bjk").value.trim();
            if(!input) return showToast("请输入内容", "error");
            var radios = document.getElementsByName("ss_lx");
            var wan = radios[1].checked ? 1 : (radios[2].checked ? 2 : 0);
            window.location.href = "api/ss.php?wan="+wan+"&nr="+encodeURIComponent(input);
        }

        function zknr(el) { el.innerHTML = el.getAttribute('id'); }
        function tzys(wan){ var p = prompt("跳转页码:"); if(p) ymxz(Number(p)-1, wan); }
        function jhgg(s){ 
            var bg=document.querySelector('.index_bj'), gg=document.querySelector('.index_gg');
            if(s){ bg.classList.remove('opacity-0','pointer-events-none'); gg.classList.remove('scale-95'); gg.classList.add('scale-100'); }
            else { bg.classList.add('opacity-0','pointer-events-none'); gg.classList.add('scale-95'); gg.classList.remove('scale-100'); }
        }

        function siteTime(){
            setTimeout(siteTime, 1000);
            var days = Math.floor((new Date() - Date.UTC(2025,8,27,0,0,0)) / 86400000);
            document.getElementById("sitetime").innerHTML = "已甜蜜运行 "+ Math.abs(days) + " 天";
        }

        // 初始化
        window.onload = function(){
            siteTime();
            love_xzk(1); // 默认加载表白
        }

        // 点击特效彩蛋
        document.body.onclick = function(e) {
            if(e.target.closest('a,button,input,textarea,.tab-btn')) return;
            var heart = document.createElement("span");
            heart.innerText = ["❤","🌸","✨","🍬"][Math.floor(Math.random()*4)];
            heart.style.cssText = `position:fixed;left:${e.clientX}px;top:${e.clientY}px;z-index:9999;color:#ec4899;pointer-events:none;transition:1s;font-size:1.2rem`;
            document.body.appendChild(heart);
            setTimeout(()=>{ heart.style.top = (e.clientY-100)+"px"; heart.style.opacity=0; setTimeout(()=>heart.remove(),1000); },10);
        }
    </script>
</body>
</html>