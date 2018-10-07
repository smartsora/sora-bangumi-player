     
$(document).ready(function(){
    var url=location.href;           //当前url
    var video_page=/.*#(\d+)$/.exec(url);   //正则查找
    var objSelect = document.getElementById("select");
    /*if ($("select option:last").val()==1){
	$("select#select").css("width","100%");
	$(".sora-bangumi-player .listSkip a").css("display","none");
    }*/



    if (video_page !=null){
       objSelect.value =video_page[1];  
        url = url.replace(/#\d+$/i,"");
        $('div.iframe-sora-bangumi-player').children('#bangumi-player').attr("src","/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url="+$("option[value = '"+$('#select').val()+"']").attr("data-video"));
    }else{
        $('div.iframe-sora-bangumi-player').children('#bangumi-player').attr("src","/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url="+$("option[value = '"+$('#select').val()+"']").attr("data-video"));
    }
   
    $(".sora-bangumi-player .listSkip select").change(function(){   
      var video=  $(this).val();
     // var video1= $("option:selected").attr("data-video");
      $('div.iframe-sora-bangumi-player').children('#bangumi-player').attr("src","/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url="+$("option:selected").attr("data-video"));
          
          document.location.href=url+"#"+video
        });   
       // $("select").find("option[value = '3']").attr("selected","selected");   
      //  alert( )

      $(".sora-bangumi-button-before").click(function(){  
        q= $("select").find("option:selected").val();   //当前option的value
        if(q !=1){
        q2=(--q);  
        objSelect.value =q2; //触发select
        document.location.href=url+"#"+(q2)   //改变url
        $('div.iframe-sora-bangumi-player').children('#bangumi-player').attr("src","/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url="+$("option[value = '"+$('#select').val()+"']").attr("data-video"));
       // location.reload();
	}
    });
    $(".sora-bangumi-button-under").click(function(){     
       v= $("select").find("option:selected").val();
       v1=$("select option:last").val();//获取select最后一个value
       if(v !=v1){
        v2=(++v);
        objSelect.value =v2; //触发select
        document.location.href=url+"#"+v2   //改变url
        $('div.iframe-sora-bangumi-player').children('#bangumi-player').attr("src","/wp-content/plugins/sora-bangumi-player/sora-bangumi-player.php?url="+$("option[value = '"+$('#select').val()+"']").attr("data-video"));

     //   location.reload();
	}
    });
});