function updateDynamicDiv(id, remote_url, call_back) {
    $.ajax({
        type: "POST",
        url: remote_url,
        success: function(html){
//            if(divLayers[id] != "undefined" && divLayers[id] != html) {
                // update the div layer to contain the latest html
                $(id).html(html);
//                $('#ajaxMessage').html(prevMsg);

                if(call_back != undefined) call_back();

                //divLayers[id] = html;
//            }
        }
    });
}