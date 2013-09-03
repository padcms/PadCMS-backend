// JavaScript Document

/* Watermark solution */
(function($){
//    var availableTags = [
//        "ActionScript",
//        "AppleScript",
//        "Asp",
//        "BASIC",
//        "C",
//        "C++",
//        "Clojure",
//        "COBOL",
//        "ColdFusion",
//        "Erlang",
//        "Fortran",
//        "Groovy",
//        "Haskell",
//        "Java",
//        "JavaScript",
//        "Lisp",
//        "Perl",
//        "PHP",
//        "Python",
//        "Ruby",
//        "Scala",
//        "Scheme"
//    ];
//    $( "#autocomplete" ).autocomplete({
//        source: availableTags
//    });
    function split( val ) {
        return val.split( /,\s*/ );
    }
    function extractLast( term ) {
        return split( term ).pop();
    }

    $( "#autocomplete" )
        // don't navigate away from the field on tab when selecting an item
        .bind( "keydown", function( event ) {
            if ( event.keyCode === $.ui.keyCode.TAB &&
                $( this ).data( "ui-autocomplete" ).menu.active ) {
                event.preventDefault();
            }
        })
        .autocomplete({
            source: function( request, response ) {
                // delegate back to autocomplete, but extract the last term
//                response( $.ui.autocomplete.filter(
//                    availableTags, extractLast( request.term ) ) );

//                $termName = $.ui.autocomplete.filter(
//                    availableTags, extractLast( request.term ) );

                $termName = extractLast( request.term );

                //console.log($( "#autocomplete" ).val());
                $.ajax({
                    url: "/issue/tag-autocomplete/aid/" + window.appId,
                    dataType: "json",
                    data: {
                        ns: $termName,
                        et: $( "#autocomplete" ).val()
                    },
                    success: function( data ) {
//                        console.log("sdd");
                        //console.log(data);
//                        response( $.map( data, function( item ) {
//                            //console.log(item);
//                            return {
//                                label: item.title,
//                                value: item.title
//                            }
//                        }));

                      response(data);
                    }
                });
            },


            minLength: 1,

            focus: function() {
                // prevent value inserted on focus
                return false;
            },
            select: function( event, ui ) {
                var terms = split( this.value );
                // remove the current input
                terms.pop();
                // add the selected item
                terms.push( ui.item.value );
                // add placeholder to get the comma-and-space at the end
                terms.push( "" );
                this.value = terms.join( ", " );
                return false;
            },

            open: function() {
                $( this ).removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
            },
            close: function() {
                $( this ).removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
            }
        });

})(jQuery);
