$(document).ready(function() {
    
    var appWidthBreakPoint = 1300; // hodnota, pri ktorej sa skryva/zobrazuje sidebar
    var appSidebarWidth = $("#appSideBar").width();  // sirka sidebaru (natiahne z CSS)
    
    // ak sa klikne na overlay, tak sidebar sa ma skryt
    $("#overlay").click(function(){
        appHideSideBar(true);
    });

    $("#closeButton a").click(function(){
        appHideSideBar(true);
    });

    // klknutie, aby sa zobrazil sidebar
    $("#menuButton").click(function(){
        appShowSideBar(true);
    });

    // zachytavanie zmensovania / zvacsovania prehliadaca
    window.onresize = function(event) {
        appInitPanels();
    };
    
    // zrusit sidebar a nastavit do default stavu
    function destroySidebar(){
        $("#appSideBar").removeClass("fixed");
    }

    // skryt sidebar    
    function appHideSideBar(useAnimation){
        $("#appSideBar").addClass("fixed");
        $("#overlay").hide();
        
        if(useAnimation){
            $('#appSideBar').animate({"left": -appSidebarWidth });
        }else{
            $("#appSideBar").css("left", -appSidebarWidth);
        }
    }
    
    // zobrazit sidebar
    function appShowSideBar(useAnimation){
        $("#overlay").show();
        var oldZIndex = $("#overlay").css("z-index");
        $("#appSideBar").css("z-index", oldZIndex+1)
        
        if(useAnimation){
            $('#appSideBar').animate({"left": "0px" });
        }else{
            $("#appSideBar").css("left", "0px");
        }
    }
    
    // podla breakpointu sa rozhodnut ci zobrazit sidebar alebo nie
    function appInitPanels(){
        if (window.outerWidth > appWidthBreakPoint) {
            $("#closeButton").hide();
            $("#menuButton").hide();
            destroySidebar();
        }else{
            $("#closeButton").show();
            $("#menuButton").show();
            appHideSideBar();
        }
    }
   
    // toto sa spusta stale pri nacitani stranky
    appInitPanels();
});

/* funkcie pre event listenery */
function handleInputTrim(e) {
    var el = this;
    window.setTimeout(function() {
        el.value = el.value.trim();
    }, 0);
}

function handleInputECV(e) {
    if (e.keyCode == 109 || e.keyCode == 173) { //it does't allow user to enter minus(-) symbol
        e.preventDefault();
    }
}

function handleInputRC(e) {
    if (e.keyCode == 111 || e.keyCode == 191) { //it does't allow user to enter symbol /
        e.preventDefault();
    }
}

function removeNonNumericChars(e){
    var el = this;
    window.setTimeout(function() {
        el.value = el.value.replace(/\D/g,'');
    }, 0);
}

function removeSlashRC(e){
    var el = this;
    window.setTimeout(function() {
        el.value = el.value.replace('/', '');
    }, 0);
}

function handleInputAllowOnlyNumbers(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+V, Command+V
        (e.keyCode === 86 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
}