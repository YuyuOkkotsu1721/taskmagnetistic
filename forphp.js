$(document).ready(function(){
    $("#saveBtn").click(function(){
        var VentureTitle = $("#VentureTitle").val();
        if(VentureTitle){
            $.post("insertventure.php", {
                VentureTitle: VentureTitle
            }, function(data, status){
                alert("Data: " + data + "\nStatus: " + status);
                $("#VentureTitle").val(""); // clear input
                loadVentures(); // Load ventures to update the list
            });
        } else {
            alert("Venture title is required!");
        }
    });

    $(document).on("click", ".editventure", function(){
        var ventureID = $(this).closest(".ventureshape").find(".ventureID").val();
        var ventureTitle = $(this).closest(".ventureshape").find(".venturetitle").val();
        $.post("editventure.php", {
            VentureID: ventureID,
            VentureTitle: ventureTitle
        }, function(data, status){
            alert("Data: " + data + "\nStatus: " + status);
            // Optionally update UI if needed
        });
    });

    $(document).on("click", ".deleteventure", function(){
        var ventureID = $(this).closest(".ventureshape").find(".ventureID").val();
        if(confirm("Are you sure you want to delete this venture?")) {
            $.post("deleteventure.php", {
                VentureID: ventureID
            }, function(data, status){
                alert("Data: " + data + "\nStatus: " + status);
                loadVentures(); // Reload ventures to reflect the deletion
            });
        }
    });

    function loadVentures(){
        $.get("readventure.php", function(data){
            $("#venturesList").html(data);
        });
    }

    loadVentures(); // Load ventures when document is ready
});
