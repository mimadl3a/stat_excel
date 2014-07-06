$(document).ready(function(){
    $("#btnAddd").click(function(){

        $("#tblData thead").append( "<tr>"+
            "<td><input type='text' name='matricule'/></td>"+
            "<td><input type='text' name='nom'/></td>"+
            "<td><input type='text' name='prenom'/></td>"+
            "<td><input type='text' name='identifiant'/></td>"+
            "<td><input type='password' name='pwd'/></td>"+
            "<td> <img src='../images/add.png' width='20' height='20' class='btnSave'></td>"+

            "</tr>");

        $( "thead tr:last" ).css({ backgroundColor: "yellow", fontWeight: "bolder" });

        $(".btnSave").bind("click", Save);




    });
});




