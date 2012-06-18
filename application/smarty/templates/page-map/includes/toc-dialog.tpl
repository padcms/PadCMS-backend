<div id="toc-dialog" class="map-panel">
    <div id="toc-tabs">
        <ul>
            <li><a href="#toc-permanent">Permanent</a></li>
            <li><a href="#toc-current">Current</a></li>
        </ul>

        <div id="toc-permanent">
            <div>
                <a id="toc-permanent-create" class="cbutton disabled" href="#">
                    <span><span class="ico">{'Create'|translate}</span></span>
                </a>
                <a id="toc-permanent-edit" class="cbutton cbutton-green cbutton-edit disabled" href="#">
                    <span><span class="ico">{'Edit'|translate}</span></span>
                </a>
                <a id="toc-permanent-delete" class="cbutton cbutton-delete disabled" href="#">
                    <span><span class="ico">{'Delete'|translate}</span></span>
                </a>
            </div>

            <div style="clear:both;"></div>

            <div id="toc-permanent-tree"></div>
        </div>

        <div id="toc-current">
            <div class="tree_box">
                <div>
                    <a id="toc-current-create" class="cbutton disabled" href="#">
                        <span><span class="ico">{'Create'|translate}</span></span>
                    </a>
                    <a id="toc-current-edit" class="cbutton cbutton-green cbutton-edit disabled" href="#">
                        <span><span class="ico">{'Edit'|translate}</span></span>
                    </a>
                    <a id="toc-current-delete" class="cbutton cbutton-delete disabled" href="#">
                        <span><span class="ico">{'Delete'|translate}</span></span>
                    </a>
                </div>
                <div style="clear:both;"></div>
                <div id="toc-current-tree"></div>
            </div>

            <div id="toc-current-spacer">&nbsp;</div>

            <div class="page-panel toc-current-edit-item-wraper">
                <div id="toc-current-edit-item" class="cont">
                    <div class="selects">

                        <span class="labe">{'Title'|translate}</span>
                        <div class="toc">
                            <div class="form-item-wrapper">
                                <input id="toc-current-title-input" value="" type="text" class="form-text" />
                            </div>
                            <a id="toc-current-title-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
                        </div>

                        <span class="labe">{'Description'|translate}</span>
                        <div class="toc">
                            <div class="form-item-wrapper">
                                <input id="toc-current-description-input" value="" type="text" class="form-text" />
                            </div>
                            <a id="toc-current-description-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
                        </div>

                        {*<span class="labe">{'PDF page'|translate}</span>
                        <div class="toc">
                            <div class="form-item-wrapper">
                                <input id="toc-current-pdf-page-input" value="" type="text" class="form-text" />
                            </div>
                            <a id="toc-current-pdf-page-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
                        </div>*}

                        <span class="labe">{'Color'|translate}</span>
                        <div class="toc">
                            <div class="form-item-wrapper">
                                <input id="toc-current-color-input" value="" type="text" class="form-text" />
                            </div>
                            <a id="toc-current-color-btn" class="cbutton" href="#"><span><span class="ico">{'Save'|translate}</span></span></a>
                        </div>

                    </div>
                    <div style="clear: both"></div>
                    <div class="pic-widget" style="padding-bottom:10px; clear: both">

                        <div class="left">
                            <span class="labe">{'Stripe'|translate}</span>
                            <div class="data-item">
                                <div id="toc-current-stripe-thumb" class="picture"></div>
                                <span id="toc-current-stripe-title" title="" class="name"></span>
                                <a id="toc-current-stripe-delete" href="#" class="close"></a>
                            </div>

                            <span class="formats">PDF / JPEG / JPG / PNG</span>

                            <div id="toc-current-upload-stripe-btn">
                                <form action="/editor/toc-upload" class="upload-form-toc-stripe" method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="stripe" class="resource-stripe"/>
                                    </span>
                                </form>
                            </div>
                        </div>

                        <div class="right">
                            <span class="labe">{'Summary'|translate}</span>
                            <div class="data-item">
                                <div id="toc-current-summary-thumb" class="picture"></div>
                                <span id="toc-current-summary-title" title="" class="name"></span>
                                <a id="toc-current-summary-delete" href="#" class="close"></a>
                            </div>

                            <span class="formats">PDF / JPEG / JPG / PNG</span>

                            <div id="toc-current-upload-summary-btn">
                                <form action="/editor/toc-upload" class="upload-form-toc-summary" method="post">
                                    <span class="file-wrapper">
                                        <a href="#" class="cbutton"><span><span class="ico">{'Upload'|translate}</span></span></a>
                                        <input type="file" name="summary" class="resource-summary"/>
                                    </span>
                                </form>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

        </div>

        <div style="clear:both;"></div>

    </div>
</div>