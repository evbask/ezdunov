function showTooltip(element, text, options = { }){
    let default_options = { class: '', tag: 'div'};
    $.extend(default_options, options);
    let next_element = $(element).next()[0];
    let tag = default_options.tag, tooltip_class = default_options.class;
    if($(next_element).hasClass('vinyl-tooltip')){
        $(next_element).addClass(tooltip_class);
        $(next_element).html(text);
    } else {
        $(element).after('<'+tag+' class="vinyl-tooltip '+tooltip_class+'">'+text+'</'+tag+'>');
    }
}
function hideTooltip(element){
    let next_element = $(element).next()[0];
    if($(next_element).hasClass('vinyl-tooltip')){
        $(next_element).remove();
    }
}

function hideAllTooltips(tooltip_class=''){
    let tooltips_mask = '.vinyl-tooltip';
    if(tooltip_class != '') {
        tooltips_mask = tooltips_mask + '.'+tooltip_class;
    }
    $(tooltips_mask).remove();
}