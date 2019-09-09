function basename(path) {
    return path.replace(/.*\//, '');
}

function getCurrentDate(){
    let date = new Date();
    let monthes = ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'];
    let day = ''+date.getDate();
    if(day.length == 1){
        day = '0'+ day;
    }
    let month = monthes[date.getMonth()],
        year = date.getFullYear(),
        hours = date.getHours(),
        minutes = (date.getMinutes() < 10) ? '0' + date.getMinutes() : date.getMinutes();
    return (day + '.' + month + '.' + year + ' ' + hours + ':' + minutes);
}

function files_confirm_send(files){
    let names = "";
    let separator = ", ";
    let last_index = chat_files.length - 1;

    $.each(files, function(index, value){
        if(index == last_index){
            separator = "";
        }
        names+= value.name+separator;
    });

    return confirm('Вы действительно хотите отправить файлы: ' + names);
}

function parseResponse(response){
    return JSON.parse(response);
}

