/**
 * @require framework/shared/jquery-1.8.3.js
 *
 * @file    depage-upload-progress.js
 *
 * Adds an upload progress bar to a file input field.
 * 
 * Supports: 
 *  - XHR2 upload
 *  - XHR1 upload (https://developer.mozilla.org/En/XMLHttpRequest/Using_XMLHttpRequest#In_Firefox_3.5_and_later) 
 *  - PHP server side upload progress with APC - http://php.net/manual/en/book.apc.php
 *  - Fallsback to iframe with gif loader
 *  
 * copyright (c) 2006-2012 Frank Hellenkamp [jonas@depagecms.net]
 *
 * @author    Ben Wallis
 */
(function($){
    if(!$.depage){
        $.depage = {};
    };
    
    /**
     * Uploader
     * 
     * @param el - file input
     * @param index
     * @param options
     */
    $.depage.uploader = function(el, index, options){
        // To avoid scope issues, use 'base' instead of 'this' to reference this class from internal events and functions.
        var base = this;
        
        // Access to jQuery and DOM versions of element
        base.$el = $(el);
        base.el = el;
        
        // Add a reverse reference to the DOM object
        base.$el.data("depage.uploader", base);
        
        // cache the form selector
        $form = base.$el.closest('form');
        
        // store the APC_UPLOAD_PROGRESS hidden field // TODO insert dynamically from plugin?
        $upload_id = null;
        
        // remember the initial form target
        target = $form.attr('target');
        
        // plugin mode according to browser support - iframe / apc / xhr
        var mode = 'iframe';
        
        // Cache the XHR object 
        var xhr = new XMLHttpRequest();
        
        // init {{{
        /**
         * Init
         * 
         * Get the plugin options.
         * Setup onchange handlers for file inputs.
         * Add the progress bar elements.
         * 
         * @return void
         */
        base.init = function(){
            
            base.options = $.extend({}, $.depage.uploader.defaultOptions, options);
            
            if ( base.support() === 'iframe') {
                // make iframe id unique // TODO enforce
                base.options.iframe = base.el.id + '_' + base.options.iframe;
                base.iframe.build();
            };
            
            base.addProgress();
            
            // add file click handlers
            base.$el.change(function() {
                base.upload(this);
            });
        };
        // }}}
        
        /**
         * Suport
         * 
         * Determines if the browser XHR object supports upload.
         * Sends a server request to the AJAX lander which should return an apc_enabled field.
         * 
         * @returns {String}
         */
        base.support = function(){
            // TODO remove - testing apc
            if(false && typeof(xhr.upload) !== 'undefined'){
                mode = 'xhr';
            } else {
                $.ajax({
                    url : base.options.server_src,
                    dataType: 'json',
                    success : function(data) {
                        if (data.apc_enabled){
                            mode = 'apc';
                        }
                    }
                });
            }
           
            return mode;
        },
        
        // Upload {{{
        /**
         * Upload
         * 
         * Fascade for the upload call, wraps the iframe or xhr upload functions.
         * 
         * @param fileinput
         * @return void
         */
        base.upload = function(fileinput){
            switch(mode){
                case 'iframe' :
                case 'apc' :
                    base.iframe.upload();
                    break;
                case 'xhr' :
                    base.xhr.upload(fileinput);
                    break;
            }
        };
        // }}}
        
        // IFRAME {{{
        /**
         * Iframe Function Namespacing
         */
        base.iframe = {
            
            /*
             * id for the ajax progress call  
             */
            timeout_id : null,	
            
            // build {{{
            /**
             * Build
             * 
             * Creates the iframe.
             * Adds a handler for the (2nd) iframe load (upload complete).
             * Caches the hidden upload id field.
             * 
             * @return null
             */
            build : function() {
                $iframe = $('<iframe />').attr({
                    id:base.options.iframe,
                    name:base.options.iframe,
                    frameborder:0,
                    border:0,
                    src:base.options.src,
                    scrolling:'no',
                    scrollbar:'no',
                    width: 0,
                    height: 0,
                    style: 'display: none'
                }).load(function(){
                    $iframe.unbind().load(function(){
                        base.iframe.reset();
                        // clear ajax request timeouts
                        clearTimeout(base.iframe.timeout_id);
                        // TODO check status
                        base.complete();
                    });
                });
                $form.append($iframe);
                $upload_id = $('input[name=' + base.options.server_upload_key + ']');
            },
            // }}}
            
            
            // Upload {{{
            /**
             * Upload
             * 
             * Submits the form to the iframe so that uploading begins.
             * 
             * @return void
             */
            upload : function() {
                $form
                    .attr('target', base.options.iframe)
                    .submit();
                
                base.start();
                base.iframe.getProgress();
            },
            // }}}
            
            
            // {{{
            /**
             * Reset
             * 
             * Reset the frame target.
             * 
             * @return void
             */
            reset : function() {
                $form.attr('target', target);
            },
            // }}}
            
            
            // getProgress {{{
            /**
             * Get Progress
             * 
             * If in "apc" mode starts sending the AJAX upload progress requests
             * to the server AJAX lander specified by base.options.server_src,
             * the lander should return a JSON percent field. If not set, or in iframe
             * only mode the fallback loader is activated. 
             * 
             * @return void
             */
            getProgress : function(){
                if (mode=='apc'){
                    $.ajax({
                        url : base.options.server_src + '?' + $upload_id.serialize(),
                        success : function(data) {
                            if (typeof(data.percent) !== 'undefined') {
                                base.setProgress(data.percent);
                                if (percent < 100){
                                    base.iframe.timeout_id = setTimeout(base.iframe.getProgress, 250);
                                }
                            } else {
                                base.fallback();
                            }
                        }
	                });
                } else {
                    base.fallback();
                }
            }
            // }}}
            
        };
        // }}}
        
        
        // XHR {{{
        /**
         * XHR Function Namespacing
         */
        base.xhr = {
           
            // xhr.upload() {{{
            /**
             * Upload
             * 
             * Begin the XHR Upload. Handles progress and load events.
             * 
             * @param fileinput - file to upload
             */
            upload : function(fileinput){
                xhr.open('POST', base.options.src, true);
                xhr.upload.onprogress = function(e) {
                    // TODO x-browser test and fallback 
                    if (e.lengthComputable) {
                        base.setProgress( e.loaded * 100 / e.total );
                        // base.setProgress( e.position * 100 / e.totalSize );
                    }
                };
                xhr.onload = function(e) {  
                    if (xhr.status == 200) {  
                        base.complete();
                    } else {  
                        base.error();
                    }
                };
                base.start();
                xhr.send(fileinput.files[0]);
            }
            // }}}
        };
        
        
        /**
         * Cancel
         * 
         * @return
         */
        base.cancel = function(){
            // TODO
        };
        
        
        /** 
         * Error
         * 
         * @return
         */
        base.error = function(){
            // TODO
        };
        
        
        /**
         * Start
         * 
         * Setup the upload
         * 
         * @return void
         */
        base.start = function(){
            base.controls.progress.show();
        };
        
        
        /**
         * Complete
         * 
         * Upload complete
         * 
         * @return void
         */
        base.complete = function(){
            base.controls.progress.hide();
        };
        
        
        /**
         * Set Progress
         * 
         * Sets the progress percent width.
         * 
         * @param percent
         * 
         * @return void
         */
        base.setProgress = function(percent){
            base.controls.percent.width(percent + '%');
        };
        
        
        /**
         * Fallback 
         * 
         * No upload progress can be calculated.
         * 
         * Show a generic image gif loader.
         * 
         * @return void
         */
        
        base.fallback = function(){
            base.controls.percent.replaceHtml('<img src="' + base.options.loader_img + '"/>');
        };
        
        
        // {{{ addProgress()
        /**
         * addProgress
         * 
         * Add the progress elements
         * 
         * @return
         */ 
        base.addProgress = function() {
            base.controls = {
                progress : $('<span />').attr(
                    {
                        'id': base.el.id + '-progress',
                        'class' : base.options.classes.progress,
                        'style' : 'display:none;'
                    }),
                percent : $('<span />').attr(
                    {
                        'id': base.el.id + '-percent',
                        'class' : base.options.classes.percent,
                    }).width('0%'),
            };
            
            base.controls.progress.append(base.controls.percent);
            base.$el.after(base.controls.progress);
        };
        // }}}
        
        base.init();
    };
    
    /**
     * Options
     * 
     * classes : progress and percent element classes.
     * iframe : iframe id / name.
     * src : iframe source - form posted to.
     * server_src: the AJAX lander for the APC upload progress calculation
     * server_upload_key: the hidden file element name matches APC upload key.
     * loader_img: the fallback image - animated gif or similar.
     */
    $.depage.uploader.defaultOptions = {
        classes : {
           progress: 'progress',
           percent: 'percent'
        },
        src: '/documentation/examples/uploader.php',
        iframe: 'upload_target',
        server_src : '../../uploadprogress.php', // TODO absolute
        server_upload_key : 'APC_UPLOAD_PROGRESS',
        loader_img : 'libs/loader.gif'
    };
    
    $.fn.depage_uploader = function(param1, options){
        return this.each(function(index){
            (new $.depage.uploader(this, index, options));
        });
    };
    
})(jQuery);
