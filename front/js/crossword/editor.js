/**
 * Copyright (c) PadCMS (http://www.padcms.net)
 *
 * Licensed under the CeCILL-C license
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-en.html
 * http://www.cecill.info/licences/Licence_CeCILL-C_V1-fr.html
 */
    /**
    * var int Grid height in pixels
    */
    var iGridHeight = 0;
    /**
    * var int Grid width in pixels
    */
    var iGridWidth = 0;

    /**
    * var int Number of selected cells. Single selection.
    */
    var iSelectedCellsCount = 0;

    /**
    * var string Word diretion. 'horizontal' or 'vertical'
    **/
    var sDirection = '';

    var iWordId = 0;

    var CrosswordGame = {
        init: function () {
            GridContainer.init();
        }
    };

    String.prototype.format = function() {
        var formatted = this;
        for (arg in arguments) {
            formatted = formatted.replace("{" + arg + "}", arguments[arg]);
        }
        return formatted;
    };

    var GridContainer = {
        init: function () {
            $.ajax({
                url: '/field-games-crossword/get-data',
                type: 'POST',
                dataType: 'json',
                async: false,
                data: {
                    page_id: window.pid,
                    field_id: window.field_id
                },
                success: function(responseJSON) {
                    if (!responseJSON.status) {
                        if (responseJSON.message) {
                            alert(responseJSON.message);
                        } else {
                            alert(translate('Error. Can\'t get data for game'));
                        }
                    } else {
                        if (responseJSON.data) {
                            iGridHeight = parseInt(responseJSON.data.grid_width);
                            iGridWidth  = parseInt(responseJSON.data.grid_width);
                            if (responseJSON.data.words) {
                                words = responseJSON.data.words;
                            }
                        }
                    }
                },
                error: function() {
                    console.log('Error');
                }
            });

            GridContainer.fillGrid();
            GridContainer.showGridSize();

            ResizableContainer.init();
        },

        fillGrid: function() {
            $('#gridContainer').css({'height' : iGridHeight * 30, 'width' : iGridWidth * 30});
            $('.ui-dialog').css({'height' : iGridHeight * 30 + 200, 'width' : iGridWidth * 30 + 150});
            $('#dialog').css({'height' : iGridHeight * 30 + 200, 'width' : iGridWidth * 30 + 150});

            var iPieces = parseInt(iGridHeight * iGridWidth);

            var context = $('#gridContainer');

            $('#selectable li', context).detach();
            for (var i = 0; i < iPieces; i++) {
                $('#selectable', context).append('<li class="ui-state-default" id="o' + (i % GridContainer.getGridSize()[0] + 1) + 'x' + (Math.floor(i /  GridContainer.getGridSize()[1]) + 1) +  '"></li>');
            }

            for (var wordIndex in words){
                var word = words[wordIndex];
                var iStartX = parseInt(word.startX);
                var iStartY = parseInt(word.startY);

                if (word.direction == 'horizontal') {
                    var finishCoordinate = iStartX + parseInt(word.length);
                    var coordinateCounter = iStartX;
                    var classTpl = '#o{0}x' + iStartY;
                } else if (word.direction == 'vertical') {
                    var finishCoordinate = iStartY + parseInt(word.length);
                    var coordinateCounter = iStartY;
                    var classTpl = '#o' + iStartX + 'x{0}';
                }

                $('#' + word.direction + 'Questions')
                        .append('<li id="' + word.id + '">[' + iStartX + ':' + iStartY + '] ' + word.question + '<a href="/field-games-crossword/delete-word/' + word.id + '"></a></li>');

                for (var charCounter = 0; coordinateCounter < finishCoordinate; charCounter++, coordinateCounter++) {
                    var className = classTpl.format(coordinateCounter);
                    var char = $('li' + className, context);
                    char.addClass('ge-confirmed-word');
                    char.text(word.answer[charCounter]);
                }
                console.log(word);
            }



            GridContainer.bindDeleteWord();
        },

        showGridSize: function() {
            $('#hrisontalMarkers li').detach();
            for (var i = 1; i <= GridContainer.getGridSize()[0]; i++) {
                $('#hrisontalMarkers').append('<li class="ui-state-default">' + i + '</li>');
            }

            $('#verticalMarkers li').detach();
            for (var i = 1; i <= GridContainer.getGridSize()[1]; i++) {
                $('#verticalMarkers').append('<li class="ui-state-default">' + i + '</li>');
            }
        },

        getGridSize: function() {
            return [iGridWidth, iGridHeight];
        },

        isValidForm: function () {
            var bIsValid = true;

            if ($('#question').val().length < 5) {
                $( '#question' ).css({
                    'border': '1px solid red'
                });
                bIsValid = false;
            } else {
                $('#question').css({
                    'border': '1px solid #AAAAAA'
                });
            }

            return bIsValid;
        },

        bindDeleteWord: function() {
            var context = this;
            $('a', '#questionsList').bind('click', context, function(event) {
                event.data.onDeleteWord(event.originalEvent);
                return false;
            });
        },

        onDeleteWord: function(event) {
            var context = this;
            var wordId = $(event.currentTarget).parent().attr('id');
            if (!wordId) return;

            $.ajax({
                url: '/field-games-crossword/delete-word',
                type: 'POST',
                dataType: 'json',
                data: {
                    page_id: window.pid,
                    field_id: window.field_id,
                    word_id: wordId
                },
                success: function(data) {
                    try {
                        if (data.status == 1) {
                            $('li#' + wordId, '#questionsList').remove();
                        } else {
                            console.log(data);
                        }
                    } catch (e) {
                        window.ui.log(e);
                    }
                }
            });
        }
    };

    var ResizableContainer = {
        init: function () {
            $( "#selectable" ).selectable(SelectableGrid);
            $( "#dialog-form" ).dialog(FormDialog);

            $('#gridContainer').resizable({
                grid: [30, 30],
                minHeight: 330,
                minWidth: 330,

                start: function (event, ui)
                {
                },
                resize: function (event, ui)
                {
                    GridContainer.fillGrid();
                    GridContainer.showGridSize();

                    $('#selectable').css({
                        'background-color': 'lightblue'
                    });

                },
                stop: function (event, ui)
                {
                    $('#selectable').css({
                        'background-color': 'white'
                    });

                    Saver.saveSize();
                }
            });

            // This button dsables resizable functionality and starts selectable for resized area.
//            $('#disableResising').button({
//                icons: {
//                    primary: "ui-icon-locked"
//                }
//            }).click(ResizableContainer.disableResizing);
        }
    };

    var SelectableGrid = {
        stop: function (event, ui) {
            var lstSelectedCells = $('.ui-selected', $(this)).not('.ge-wait-for-confirm').not('ge-confirmed-word');
            if (lstSelectedCells.length > 2) {
                iSelectedCellsCount = lstSelectedCells.length;

                var iY1 = lstSelectedCells.first().attr('id').replace('o', '').split('x')[0];
                var iY2 = lstSelectedCells.last().attr('id').replace('o', '').split('x')[0];
                var iX1 = lstSelectedCells.first().attr('id').replace('o', '').split('x')[1];
                var iX2 = lstSelectedCells.last().attr('id').replace('o', '').split('x')[1];

                if (iY1 == iY2 && lstSelectedCells.first().attr('id') != lstSelectedCells.last().attr('id')) {
                    sDirection = 'vertical';
                } else if (iX1 == iX2 && lstSelectedCells.first().attr('id') != lstSelectedCells.last().attr('id')) {
                    sDirection = 'horizontal';
                } else {
                    // some shit happens
                    sDirection = '';
                }

                if(sDirection != '') {
                    var sCellValue = '';
                    var sReadOnlyAttribute = '';
                    var sReadOnlyBackgroundClass = '';
                    var xForbiddenCharacters = /_|\d|\W/;
                    var iDialogWidth = 50 * iSelectedCellsCount;

                    $('#wordBlock input').detach();

                    lstSelectedCells.each(function () {
                        if ($(this).text() != '') {
                            sCellValue = $(this).text();
                            sReadOnlyAttribute = 'readonly="true"';
                            sReadOnlyBackgroundClass = 'roBackground';
                        } else {
                            sCellValue = '';
                            sReadOnlyAttribute = '';
                            sReadOnlyBackgroundClass = '';
                        }
                        $('#wordBlock').append('<input type="text" name="letter[]" id="letter_' + $(this).attr('id').replace('o', '') + '" maxlength="1" class="letter ui-widget-content ui-corner-all ' + sReadOnlyBackgroundClass +'" value="' + sCellValue + '" ' + sReadOnlyAttribute + ' />');
                    });

                    $('#wordBlock').css({
                        'width': iDialogWidth + 100 + 'px'
                    });

                    $( "#dialog-form" ).dialog({width: iDialogWidth + 200 + 'px'});

                    $( '.letter' ).change().keyup(function ()
                    {
                        if( $(this).val().match(/\w/) != null && $(this).val().match(xForbiddenCharacters) == null ) {
                            $(this).next().not('[readonly="true"]').focus();
                        }
                        $(this).val($(this).val().replace(xForbiddenCharacters, ''));
                    });

                    $( "#dialog-form" ).dialog( "open" );
                    lstSelectedCells.addClass('ge-wait-for-confirm');
                }
            }
        }
    };

    var FormDialog = {
        autoOpen: false,
        modal:    true,
        minWidth: 500,
        buttons: {
            Apply: function () {
                if (GridContainer.isValidForm()) {
                    if ($('#errorOutput').css('display') != 'none') {
                        $('#errorOutput').fadeOut('slow');
                    }
                    var lstLetters = $('.ge-wait-for-confirm');
                    var sAnswer = '';
                    lstLetters.each(function ()
                    {
                        $(this).text($('#letter_' + $(this).attr('id').replace('o', '')).val());
                        $(this).removeClass('ge-wait-for-confirm');
                        $(this).addClass('ge-confirmed-word');
                        sAnswer += $(this).text();
                    });

                    var iWordStartPosition = 0;
                    var iStartX = 0;
                    var iStartY = 0;
                    var sQuestion = $('#question').val();

                    var firstSymbolCoords = lstLetters.first().attr('id').replace('o', '').split('x');
                    var iStartX = firstSymbolCoords[0];
                    var iStartY = firstSymbolCoords[1];

                    word = {
                        page_id: window.pid,
                        field_id: window.field_id,
                        startX: iStartX,
                        startY: iStartY,
                        question: sQuestion,
                        answer: sAnswer,
                        length: lstLetters.length,
                        direction: sDirection
                    }

                    Saver.saveWord(word);

                    $('#' + sDirection + 'Questions').append('<li id="' + iWordId +'">[' + iStartX + ':' + iStartY +  '] ' + sQuestion + '<a href="/field-games-crossword/delete-word/' + word.id + '"></a></li>');

                    GridContainer.bindDeleteWord();

                    $( this ).dialog( "close" );
                } else {
                    $('#errorOutput').fadeIn( 'slow' );
                }
            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function ()
        {
            $('.ge-wait-for-confirm').each(function ()
                {
                    $(this).removeClass('ge-wait-for-confirm');
                });
        },
        open: function ()
        {
            $('#errorOutput').css({'display': 'none'});

            $('#question')
                .val(null)
                .css({'border': '1px solid #AAAAAA'});
        }
    };

    var Saver = {
        saveSize: function() {
            var oResultData = {
                gridWidth: GridContainer.getGridSize()[0],
                gridHeight: GridContainer.getGridSize()[1],
                page_id: window.pid,
                field_id: window.field_id
            };

            $.ajax({
                url: '/field-games-crossword/save-size',
                type: 'POST',
                dataType: 'json',
                data: oResultData,
                async: false,
                sucess: function (data)
                {
                    if(!data.status || data.status == 0) {
                        console.log(data.message);
                    }
                }
            });
        },
        saveWord: function (word) {
            var oResultData = {
                page_id: window.pid,
                field_id: window.field_id,
                word: word
            };
            $.ajax({
                url: '/field-games-crossword/save-word',
                type: 'POST',
                dataType: 'json',
                data: oResultData,
                async: false,
                success: function (data)
                {
                    if(!data.status || data.status == 0) {
                        console.log(data.message);
                    } else {
                        iWordId = data.word_id;
                    }
                },
                errror: function () {
                    console.log('Error!');
                }
            });
        }
    };