$(document).ready(function(){
    $("#btnAddd").click(function(){

        $("#tblData tbody").append( "<tr>"+
            "<td><input type='text'/></td>"+
            "<td><input type='text'/></td>"+
            "<td><input type='text'/></td>"+
            "<td><input type='text'/></td>"+
            "<td><input type='password'/></td>"+

            "</tr>");

    });
});


 