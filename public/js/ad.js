var counter = $("#annonce_images .row").length;

$("#add_image").click(function(){

    // récupére le prototype
    var tmpl = $("#annonce_images").data("prototype");

    // incrémentation du nom des pototype a chaque clique
    tmpl = tmpl.replace(/__name__/g,counter++)

    // insére le prototype après le bloc avec un id #annonce_images
    $("#annonce_images").append(tmpl);

    //console.log(tmpl);

    deleteBlock();
    
    });


        function deleteBlock (){

            $('.del_image').click(function(){

            var id = $(this).data("bloc");
            
            console.log(id);

            $("#"+id).remove();
            })

        }


deleteBlock();



