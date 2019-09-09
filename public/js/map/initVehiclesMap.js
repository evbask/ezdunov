ymaps.ready(init);

var myMap;

function init(){
    myMap = new ymaps.Map("map", {
        center: [59.99, 30.31],
        zoom: 15
    });

    var objectManager = new ymaps.ObjectManager();
    getVehicles(objectManager, myMap);
    myMap.geoObjects.add(objectManager);

    $(window).resize(function(){

        $('.fixed-sidebar-left').on('transitionend webkitTransitionEnd oTransitionEnd', function () {
            myMap.container.fitToViewport();
        });

    });

}

function getVehicles(objectManager, myMap){
    $.get('/getLKObject').done(function(data){
        objectManager.add(data);
        var bounds = objectManager.getBounds();
        myMap.setBounds(bounds,{checkZoomRange:true});

    });
    return objectManager;
}



