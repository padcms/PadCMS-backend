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

    var oResultData = {
        words: {},
        gridWidth: 0,
        gridHeight: 0,
        page_id: 0,
        field_id: 0
    };

    var iWordsCounter = 0;

    var CrosswordGame = {
        init: function () {
            GridContainer.init();
        }
    };

    var GridContainer = {
        init: function () {
            GridContainer.fillGrid();
            GridContainer.showGridSize();

            ResizableContainer.init();
        },

        fillGrid: function() {
            iGridHeight = parseInt($('#selectable').css('height'));
            iGridWidth  = parseInt($('#selectable').css('width'));
            var iPieces = parseInt((iGridHeight * iGridWidth) / 900);

            $('#selectable li').detach();
            for (var i = 0; i < iPieces; i++) {
                $('#selectable').append('<li class="ui-state-default" id="o' + (i % GridContainer.getGridSize()[0] + 1) + 'x' + (Math.floor(i /  GridContainer.getGridSize()[1]) + 1) +  '"></li>');
            }
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
            return [iGridWidth / 30, iGridHeight / 30];
        },

        prepareData: function() {
            oResultData.gridWidth  = GridContainer.getGridSize()[0];
            oResultData.gridHeight = GridContainer.getGridSize()[1];
            oResultData.page_id    = window.pid;
            oResultData.field_id   = window.field_id;
        },

        isValidForm: function () {
            var bIsValid = true;

            if ($('#question').val().length < 10) {
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
        }
    };

    var ResizableContainer = {
        init: function () {
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
                }
            });

            // This button dsables resizable functionality and starts selectable for resized area.
            $('#disableResising').button({
                icons: {
                    primary: "ui-icon-locked"
                }
            }).click(ResizableContainer.disableResizing);
        },

        disableResizing: function () {
            $('#gridContainer').resizable('disable');
            $('#gridContainer').removeClass('ui-state-disabled');
            $(this).remove();

            // Initialization of selectable grid
            $( "#selectable" ).selectable(SelectableGrid);

            $( "#dialog-form" ).dialog(FormDialog);

            $('#saveAndExit').button({
                icons: {
                    primary: "ui-icon-disk"
                }
            }).click(Saver.save);

            $('#saveAndExit').show();
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
                        'width': iDialogWidth + 'px'
                    });

                    $( "#dialog-form" ).dialog({width: iDialogWidth + 100});

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
                    var sQuestion = $('#question').val();

                    if (sDirection == 'vertical') {
                        iWordStartPosition = lstLetters.first().attr('id').replace('o', '').split('x')[0];
                        $('#verticalQuestions').append('<li id="q' + iWordStartPosition +'">' + iWordStartPosition + '. ' + sQuestion + '</li>');
                    } else {
                        iWordStartPosition = lstLetters.first().attr('id').replace('o', '').split('x')[1];
                        $('#horizontalQuestions').append('<li id="q' + iWordStartPosition +'">' + iWordStartPosition + '. ' + sQuestion + '</li>');
                    }

                    oResultData.words[iWordsCounter] = {
                        startFrom: iWordStartPosition,
                        question: sQuestion,
                        answer: sAnswer,
                        lenght: lstLetters.length,
                        direction: sDirection
                    }

                    iWordsCounter++;

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
        save: function () {
            GridContainer.prepareData();

            $.ajax({
                url: '/field-games-crossword/save',
                type: 'POST',
                dataType: 'json',
                data: oResultData,
                sucess: function (data)
                {
                    $('#dialog').dialog('destroy');
                    console.log('Saving data');
                    if(!data.status || data.status == 0) {
                        console.log(data.message);
                    } else {
                        console.log('Saving data');
                        $('#dialog').dialog('close');
                    }
                }
            });
        }
    };