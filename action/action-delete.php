<script>
    window.onload = function () {
        var param = window.location.search;
        
        confirm("¿Estás seguro de querer eliminar este libro de Favoritos?");
        location="action-delete-2.php" + param;
    };
</script>

