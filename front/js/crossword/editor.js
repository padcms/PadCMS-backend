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

    //The minimum width of the grid - it is maximum X coordinate of the hosrizontal words
    var iMinWidth  = 11;
    //The minimum height of the grid - it is maximum Y coordinate of the vertical words
    var iMinHeight = 11;

    var iCellSize = 30;

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

    var WordsContainer = {
        words: {},
        addWord: function (word) {
            this.words[word.id] = word;
        },
        deleteWord: function (id) {
            delete this.words[id];
        },
        getWords: function () {
            return this.words;
        },
        getWord: function (id) {
            return this.words[id];
        },
        getMaxWidth: function() {
            var maxWidth = 0;

            $.each(this.words, function (wordId, word) {
                var length = 0;
                if ('vertical' == word.direction) {
                    length = parseInt(word.startX);
                } else if ('horizontal' == word.direction) {
                    length = parseInt(word.startX) + parseInt(word.length) - 1;
                }

                maxWidth = (length > maxWidth)? length : maxWidth;
            });

            return maxWidth;
        },
        getMaxHeight: function() {
            var maxHeight = 0;

            $.each(this.words, function (wordId, word) {
                var length = 0;
                if ('vertical' == word.direction) {
                    length = parseInt(word.startY) + parseInt(word.length) - 1;
                } else if ('horizontal' == word.direction) {
                    length = parseInt(word.startY);
                }

                maxHeight = (length > maxHeight)? length : maxHeight;
            });

            return maxHeight;
        }
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
                            iGridHeight = parseInt(responseJSON.data.grid_height);
                            iGridWidth  = parseInt(responseJSON.data.grid_width);
                            if (responseJSON.data.words) {
                                $.each(responseJSON.data.words, function(key, word) {
                                    WordsContainer.addWord(word);
                                });
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
            GridContainer.fillAnswers();

            ResizableContainer.init();
        },

        fillGrid: function() {
            $('#gridContainer').css({'height' : iGridHeight * iCellSize, 'width' : iGridWidth * iCellSize});
            $('.ui-dialog').css({'height' : iGridHeight * iCellSize + 200, 'width' : iGridWidth * iCellSize + 150});
            $('#dialog').css({'height' : iGridHeight * iCellSize + 200, 'width' : iGridWidth * iCellSize + 150});

            var context = $('#gridContainer');

            $('#selectable li', context).detach();
            for (var y = 1; y <= iGridHeight; y++) {
                for (var x = 1; x <= iGridWidth; x++) {
                    $('#selectable', context).append('<li class="ui-state-default" id="o' + x + 'x' + y +  '"></li>');
                }
            }

            $.each(WordsContainer.getWords(), function (wordId, word){
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

                for (var charCounter = 0; coordinateCounter < finishCoordinate; charCounter++, coordinateCounter++) {
                    var className = classTpl.format(coordinateCounter);
                    var char = $('li' + className, context);
                    char.addClass('ge-confirmed-word');
                    char.addClass('word-id-' + word.id);
                    char.text(word.answer[charCounter]);
                }
            });
        },

        fillAnswers: function() {
            $.each(WordsContainer.getWords(), function (wordId, word){
                var iStartX = parseInt(word.startX);
                var iStartY = parseInt(word.startY);

                $('#' + word.direction + 'Questions')
                        .append('<li id="' + word.id + '">[' + iStartX + ':' + iStartY + '] ' + word.question + '<a href="/field-games-crossword/delete-word/' + word.id + '"></a></li>');

            });
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
            var question = $('#question').val();

            var makeError = function () {
                $( '#question' ).css({
                    'border': '1px solid red'
                });
            };

            if (question.length < 5 || question.match(/^[a-zA-Z \!\.\,]+$/) == null) {
                makeError();
                return false;
            }
            var hasErrors = false;
            var lstLetters = $('input.letter', '#wordBlock');
            lstLetters.each(function ()
            {
                if ($(this).val() == '') {
                    hasErrors = true;
                }
            });

            if (hasErrors) {
                makeError();
                return false;
            }

            $('#question').css({
                'border': '1px solid #AAAAAA'
            });

            return true;
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
                            $('li.word-id-' + wordId, '#selectable').each(function(){
                                $(this).removeClass('word-id-' + wordId);
                                var liClass = $(this).attr('class');
                                var result = liClass.search(/word\-id\-\d/i);
                                if (-1 == result) {
                                    $(this).removeClass('ge-confirmed-word');
                                    $(this).removeClass('ui-selected');
                                    $(this).text('');
                                }
                            });

                            $('li#' + wordId, '#questionsList').remove();
                            WordsContainer.deleteWord(wordId);
                            ResizableContainer.resetSize();
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

            var minHeight = WordsContainer.getMaxHeight() * iCellSize;
            var minWidth = WordsContainer.getMaxWidth() * iCellSize;

            $('#gridContainer').resizable({
                grid: [iCellSize, iCellSize],
                minHeight: minHeight,
                minWidth: minWidth,

                start: function (event, ui)
                {
                },
                resize: function (event, ui)
                {
                    iGridHeight = parseInt($('#selectable').css('height'))/iCellSize;
                    iGridWidth = parseInt($('#selectable').css('width'))/iCellSize;

                    GridContainer.fillGrid();
                    GridContainer.showGridSize();
                },
                stop: function (event, ui)
                {
                    $('#selectable').css({
                        'background-color': 'white'
                    });

                    GridContainer.fillGrid();
                    GridContainer.showGridSize();
                    Saver.saveSize();
                }
            });
        },
        resetSize: function() {
            var minHeight = WordsContainer.getMaxHeight() * iCellSize;
            var minWidth = WordsContainer.getMaxWidth() * iCellSize;
            $('#gridContainer').resizable({minHeight: minHeight, minWidth: minWidth});
        }
    };

    var SelectableGrid = {
        stop: function (event, ui) {
            var lstSelectedCells = $('.ui-selected', $(this)).not('.ge-wait-for-confirm');

            //Disables adding word to the cells whichalready filled
            //var lstFilledCells = lstSelectedCells.filter('.ge-confirmed-word');
            //if (lstSelectedCells.length > 2 && lstFilledCells.length != lstSelectedCells.length) {

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

                    word.id = iWordId;
                    WordsContainer.addWord(word);

                    lstLetters.each(function ()
                    {
                        $(this).addClass('word-id-' + iWordId);
                    });

                    $('#' + sDirection + 'Questions').append('<li id="' + iWordId +'">[' + iStartX + ':' + iStartY +  '] ' + sQuestion + '<a href="/field-games-crossword/delete-word/' + word.id + '"></a></li>');

                    GridContainer.bindDeleteWord();

                    ResizableContainer.resetSize();

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
                    $(this).removeClass('ge-wait-for-confirm ui-selected');
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