<style>
    {literal}
        #gridDefinitions { width: 240px; height: 100px; margin: 0 auto; }
        .gridDefinition { float: left; width: auto; height: 64px; color: #555555; font-size: 64px; font-family: Helvetica; }
        #gridContainer { width: 660px; height: 660px; top: 60px; left: 20px; margin: 0 auto; font-family: Helvetica, Arial, sans-serif; }
        #controlsPanel { width: 200px; height: auto; margin: 0 auto; }

        #questionsList { width: 500px; margin-top: 10px; min-height: 200px; }
        #questionsList ol { list-style-type: none; }

        #feedback { font-size: 1.4em; }
        #selectable { display: block; width: 100%; height: 100%; position: relative; }
        #selectable .ui-selecting { background: #FECA40; }
        #selectable .ui-selected { background: #F39814; color: white; }
        #selectable { list-style-type: none; margin: 0; padding: 0; }
        #selectable li { margin: 3px; padding: 1px; float: left; width: 20px; height: 20px; font-size: 4em; text-align: center; }

        #hrisontalMarkers { list-style-type: none; margin: 0; padding: 0; display: block; width: 100%; height: 30px; position: absolute; margin-top: -32px; margin-left: -1px; }
        #hrisontalMarkers li { margin: 3px; padding: 1px; float: left; width: 20px; height: 20px; font-size: 4em; text-align: center; background: #E6E6E6 !important; }

        #verticalMarkers { list-style-type: none; margin: 0; padding: 0; display: block; width: 30px; height: 100%; position: absolute; margin-left: -32px; top: 0px; margin-top: -1px; }
        #verticalMarkers li { margin: 3px; padding: 1px; float: left; width: 20px; height: 20px; font-size: 4em; text-align: center; background: #E6E6E6 !important; }

        #saveAndExit { display: none; }
        #dialog-form { display: none; }

        label, input { display:block; }
        input.text { margin-bottom:12px; width:95%; padding: .4em; }
        #wordBlock { margin-bottom:12px; width:95%; height: 35px; }
        fieldset { padding:0; border:0; margin-top:25px; }
        h1 { font-size: 1.2em; margin: .6em 0; }
        .ui-dialog .ui-state-error { padding: .3em; }
        .validateTips { border: 1px solid transparent; padding: 0.3em; }
        .questionArea { height: 200px; width: 95%; }
        .ui-state-default { font-size: 16px !important; color: black; }
        .ge-confirmed-word { background: #F39814 !important; }
        #errorOutput { font-size: 11px; font-weight: 400; display: none; }
        input.letter { width: 33px; float: left; padding: 5px; margin-bottom:12px; margin-right: 10px; text-align: center; }
        .roBackground { background: #D3D3D3; }
        #questionsList li a {
            background: url("/images/map/ico-del-tag.png") no-repeat scroll 0 1px transparent;
            display: inline-block;
            height: 11px;
            margin: 0 0 0 3px;
            width: 11px;
        }
    {/literal}
</style>

<div id="gridContainer" class="ui-widget-content">
    <ol class="ui-widget-content" id="hrisontalMarkers"></ol>
    <ol id="selectable" style="clear: both;"></ol>
    <ol class="ui-widget-content" id="verticalMarkers"></ol>
    <div id="questionsList" >
        Vertical:
        <ol id="verticalQuestions"></ol>
        Horizontal:
        <ol id="horizontalQuestions"></ol>
    </div>
</div>


<div id="dialog-form" title="Edit question" class="ui-widget">
    <div id="errorOutput" class="ui-state-error ui-corner-all">
        <p>
            <span class="ui-icon ui-icon-alert " style="float: left; margin-right: .3em;"></span>
            <span id="validationMessage">
                <strong>Alert: </strong>
                <span>Please, fill the form correctly.</span>
            </span>
        </p>
    </div>

    <form>
    <fieldset>
            <label for="wordBlock">Word</label>
            <div id="wordBlock"></div>
            <label for="question">Question</label>
            <textarea id="question" name="question" class="questionArea textarea-wrapper ui-widget-content ui-corner-all"></textarea>
    </fieldset>
    </form>
</div>

