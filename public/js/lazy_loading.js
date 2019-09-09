$(document).ready(function () {
   let startFrom = 0;
   let perPage;
   let rentsCount;
   let inProgress = false;

   setInitialState();

   $('#get_more').click(function(){
       getRents();
   });

   function setInitialState(){
       $.ajax({
           url: '/get_options',
           method: 'GET',
       }).done(function (options) {
           rentsCount = options[0];
           perPage = options[1];
           if(rentsCount>perPage){
                $('#get_more').show();
           }
           getRents();
       });
   }

   function getRents(){

       if(!inProgress){
           $.ajax({
               url: '/get_more',
               method: 'GET',
               data: {'startFrom': startFrom},
               beforeSend: function () {
                   inProgress = true;
               }
           }).done(function (rents) {
               $.each(rents, function (index, rent) {
                   let rent_row = '<tr>' +
                       '<td><a href="javascript:void(0)">'+rent["id"]+'</a></td> ' +
                       '<td><span class="text-muted"><i class="fa fa-clock-o"></i> '+rent["time"]+' </span></td> ' +
                       '<td><span class="text-muted"><i class="fa fa-clock-o"></i> '+rent["created_at"]+'</span></td> ' +
                       '<td>'+rent["balancePayment"]+'</td> ' +
                       '<td>'+rent["bonusPayment"]+'</td> ' +
                       '<td>'+rent["resultPaymentAmount"]+'</td> ' +
                       '<td><div class="label label-table label-'+rent["class"]+'">'+rent["status"]+'</div></td> ' +
                       '<td>'+rent["price"]+'</td> ' +
                       '<td>'+rent["type"]+'</td>' +
                       '</tr>';

                   $('#rent_table').append(rent_row);

               });

               inProgress = false;
               startFrom += perPage;

               if(startFrom>=rentsCount){
                   $('#get_more').hide();
               }
           });
       }
   }
});