(function($){
  $(function(){
    var frame;
    var $open  = $('#roibot-media-open');
    var $clear = $('#roibot-media-clear');
    var $input = $('#roibot_avatar_ids');
    var $wrap  = $('#roibot-avatars-preview');

    function render(ids){
      $wrap.empty();
      if(!ids.length) return;
      ids.forEach(function(id){
        var $box=$('<div/>',{css:{
          width:64,height:64,borderRadius:'50%',border:'1px solid #ddd',
          display:'flex',alignItems:'center',justifyContent:'center',
          fontSize:12,overflow:'hidden'
        }}).text('#'+id);
        $wrap.append($box);
      });
    }

    function setIds(ids){
      $input.val(JSON.stringify(ids||[]));
      render(ids||[]);
    }

    try{ setIds(JSON.parse($input.val()||'[]')); }catch(e){ setIds([]); }

    $open.on('click',function(e){
      e.preventDefault();
      if(frame){ frame.open(); return; }
      frame=wp.media({
        title:ROIBOT_MEDIA.title,
        button:{text:ROIBOT_MEDIA.button},
        multiple:ROIBOT_MEDIA.multiple?'add':false,
        library:{type:'image'}
      });
      frame.on('select',function(){
        var ids=[];
        frame.state().get('selection').each(function(att){ ids.push(att.id); });
        setIds(ids);
      });
      frame.open();
    });

    $clear.on('click',function(e){ e.preventDefault(); setIds([]); });
  });
})(jQuery);
