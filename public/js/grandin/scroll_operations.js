function initScroll() {
    msgContainer.slimscroll(options);
}

function setScroll() {
    let scrollTo_int = msgContainer.prop('scrollHeight') + 'px';
    options.scrollTo = scrollTo_int;
    msgContainer.slimscroll(options);
}

function removeScroll() {
    msgContainer.slimScroll({destroy: true});
    $(".slimScrollBar,.slimScrollRail").remove();
}

function goBottom(){
    removeScroll();
    setScroll();
}

function isAtBottom(){
    let current_position = Math.ceil(msgContainer.innerHeight() + msgContainer.scrollTop());
    let scroll_height = msgContainer.prop('scrollHeight');
    return (scroll_height - current_position <= 200);
}

