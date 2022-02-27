var APP = APP || {};  // namespace

/**
 * utils pre Ajax
 */
APP.ajaxUtils = {
    /**
     * potvrdenie formulara cez ajax s parametrami:
     * options.idForm ... idFormulara, ktory chceme submitnut
     * options.doBeforeSubmit() ... co sa ma urobit pred submitom
     * options.doOnSuccess() ... co sa ma urobit pri ukonceni ajax volania
     * options.doOnError() ... co sa ma urobit v pripade chyby
     * options.serverUrl ... url kam sa posle request
     * options.targetElement ... id elementu, kde sa zobrazi response
     */
    submitFormByOptions : function(options){
        $("#"+options.idForm).submit(function(e) {
            e.preventDefault();
            options.doBeforeSubmit();

            //--- ak existuju instancie ckeditora, tak ich obsah presunieme do prislusnych textarei--------------
            var textareaId;
            var textarea;
            var elementsForm;

            if(typeof(CKEDITOR) != "undefined") {
                for (var i in CKEDITOR.instances) {
                    textareaId = CKEDITOR.instances[i].name;
                    textarea = document.getElementById(textareaId);
                    if (textarea != null) {
                        elementsForm = textarea.form;
                    }
                    if ($(elementsForm).attr('name') == $("#" + options.idForm).attr('name')) {  // kontrola ci textarea patri do potvrdeneho formulara
                        textarea.value = CKEDITOR.instances[textareaId].getData();
                    }
                    CKEDITOR.instances[i].destroy(true);
                }
            }

            var formData = new FormData($(this)[0]);  // tento sposob je novy - pre formulare, kde je aj file upload

            $.ajax({
                type: "POST",
                url: options.serverUrl,
                data: formData,
                async: false,
                processData: false,
                contentType: false,
                success: function(data) {
                    $("#"+options.targetElement).html(data);
                    options.doOnSuccess();
                },
                error: function(){
                    alert('Chyba ! submit ajax form');
                    options.doOnError();
                }
            });
        });
    },

    /**
     * potvrdenie formulara cez ajax
     */
    submitFormIntoDiv : function(theIdForm, theUrl, theTargetDiv, theIdElementsToDisable, theIdElementsToOverlay){
        $("#"+theIdForm).submit(function(e) {
            // disable elements
            if(typeof(theIdElementsToDisable) != "undefined") {
                if (theIdElementsToDisable.length > 0) {
                    for (var i = 0; i < theIdElementsToDisable.length; i++) {
                        $("#" + theIdElementsToDisable[i]).prop('disabled', true);
                    }
                }
            }

            // overlay elements
            if(typeof(theIdElementsToOverlay) != "undefined") {
                if (theIdElementsToOverlay.length > 0) {
                    for (var i = 0; i < theIdElementsToOverlay.length; i++) {
                        APP.uiHelpers.createOverlay(theIdElementsToOverlay[i]);
                    }
                }
            }

            e.preventDefault();
            //$('#ajax-loading').fadeIn('fast');

            //--- ak existuju instancie ckeditora, tak ich obsah presunieme do prislusnych textarei--------------
            var textareaId;
            var textarea;
            var elementsForm;

            if(typeof(CKEDITOR) != "undefined") {
                for (var i in CKEDITOR.instances) {
                    textareaId = CKEDITOR.instances[i].name;
                    textarea = document.getElementById(textareaId);
                    if (textarea != null) {
                        elementsForm = textarea.form;
                    }
                    if ($(elementsForm).attr('name') == $("#" + theIdForm).attr('name')) {  // kontrola ci textarea patri do potvrdeneho formulara
                        textarea.value = CKEDITOR.instances[textareaId].getData();
                    }
                    CKEDITOR.instances[i].destroy(true);
                }
            }

            var formData = new FormData($(this)[0]);  // tento sposob je novy - pre formulare, kde je aj file upload

            $.ajax({
                type: "POST",
                url: theUrl,
                data: formData,
                //async: false,
                //dataType: "html",
                processData: false,
                contentType: false,
                success: function(data) {
                    $("#"+theTargetDiv).html(data);
                    //$('#ajax-loading').fadeOut('fast');
                    // zrusit disabled
                    if(typeof(theIdElementsToDisable) != "undefined") {
                        if (theIdElementsToDisable.length > 0) {
                            for (var i = 0; i < theIdElementsToDisable.length; i++) {
                                $("#" + theIdElementsToDisable[i]).prop('disabled', false);
                            }
                        }
                    }

                    // zrusit overlay
                    if(typeof(theIdElementsToOverlay) != "undefined") {
                        if (theIdElementsToOverlay.length > 0) {
                            for (var i = 0; i < theIdElementsToOverlay.length; i++) {
                                APP.uiHelpers.destroyOverlay(theIdElementsToOverlay[i]);
                            }
                        }
                    }
                },
                error: function(xhr){
                    console.log(xhr.responseText);
                    alert('Chyba ! submit ajax form');
                }
            });
        });
    },

    /**
     * ajax volanie url
     * - ocakava sa ze zo servera pride html a to sa 'zobrazi/spusti' vo vygenerovanom elemente.
     * - na konci sa vygenerovany element odstrani z DOM
     */
    callUrl : function(theUrl, funcBefore, funcAfter){
        var idElement = "";
        var randElId = "";
        do {
            randElId = "el" + Math.floor(Math.random()*(2000-1+1)+1).toString();
            if (document.getElementById(randElId) == null) {
                idElement = randElId;
            }
        } while(idElement === "");
        $('body').append('<div id="'+idElement+'"></div>');
        this._ajaxCall(idElement, theUrl, true, funcBefore, funcAfter);
    },

    /**
     * ajax volanie url
     * - ocakava sa ze zo servera pride html a to sa 'zobrazi/spusti' v elemente, ktory ma id 'theIdElement'
     */
    callUrlIntoElement : function(theIdElement, theUrl, funcBefore, funcAfter){
        this._ajaxCall(theIdElement, theUrl, false, funcBefore, funcAfter);
    },

    /**
     * pomocna "privatna" funkcia
     */
    _ajaxCall : function(theIdElement, theUrl, theRemoveTargetElement, funcBefore, funcAfter){
        $.ajax({
            url: theUrl,
            beforeSend: function() {
                $("#isAppError").val("1");
                $('#ajax-loading').fadeIn('fast');
                if('undefined' !== funcBefore && (typeof funcBefore === "function")){
                    funcBefore();
                }
            },
            success: function(data){
                if(theRemoveTargetElement){
                    $('#' + theIdElement).html(data).remove();
                }else{
                    $('#' + theIdElement).html(data);
                }
            },
            error: function(xhr) { // if error occured
                alert(" Error occured.please try again");
            },
            complete: function() {

                $('#ajax-loading').fadeOut('fast');
                if($("#isAppError").val() == "1"){
                    //alert("Chyba v aplikácii!");
                }
                if('undefined' !== funcAfter && (typeof funcAfter === "function")){
                    funcAfter();
                }
            },
            dataType: 'html'
        });
    }
};

/**
 * utils pre ckeditor
 */
APP.uiHelpers = {
    createOverlay : function(theIdElement) {
        let origin = $("#"+theIdElement);
        let idElementOverlay = theIdElement + "_paOl";
        origin.css({"position":"relative"});

        let overlay = jQuery('<div id="'+idElementOverlay+'" class="ajax-overlay text-center">Nahrávam</div>');
        overlay.css({
            "top": 0,
            "left": 0,
            "width": origin.outerWidth(),
            "height": origin.outerHeight()
        });
        overlay.appendTo(origin);
    },

    destroyOverlay : function(theIdElement) {
        let idElementOverlay = theIdElement + "_paOl";
        $("#"+idElementOverlay).remove();
    }
};

/**
 * utils pre DOM
 */
APP.dom = {
    toggleBlockVisibility : function(theIdElement, func1, func2) {
        let element = $("#"+theIdElement);
        if(element.css('display') === 'none'){
            element.show();
            if('undefined' !== func1 && (typeof func1 === "function")){
                func1();
            }
        }else{
            element.hide();
            if('undefined' !== func2 && (typeof func2 === "function")){
                func2();
            }
        }
    }
};

/**
 * utils pre ckeditor
 */
APP.ckeditorUtils = {
    /**
     * pred potvrdenim formulara cez Ajax treba skopirovat obsah CKeditora do jeho instancie
     */
    setTextareaFromCKEditor : function(theIdTextarea) {
        document.getElementById(theIdTextarea).value = CKEDITOR.instances[theIdTextarea].getData();
    }
};

APP.cookieUtils = {
    setCookie : function(name, value, days){
        var expires;
        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        } else {
            expires = "";
        }
        document.cookie = encodeURIComponent(name) + "=" + encodeURIComponent(value) + expires + "; path=/";
    },

    getCookie : function(name){
        var nameEQ = encodeURIComponent(name) + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return decodeURIComponent(c.substring(nameEQ.length, c.length));
        }
        return null;
    },

    removeCookie : function(){
        APP.cookieUtils.setCookie(name, "", -1);
    }
};
