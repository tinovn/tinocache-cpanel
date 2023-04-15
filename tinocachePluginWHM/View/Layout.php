<!-- WHM css library -->
<link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>:2087/cPanel_magic_revision_1392312282/3rdparty/bootstrap/optimized/css/bootstrap.min.css" >
<link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>:2087/cPanel_magic_revision_1478498023/combined_optimized.css" >
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" >
<link href="../tinocachePlugin/View/Assets/tinocachePlugin.css" rel="stylesheet" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script>

    $(".title_sec").hide().html("");
<?php if (isset($_GET['controller']) && $_GET['controller'] === 'Logs'): ?>

        $(document).ready(function () {
            $('#table1').DataTable({
                "order": [[0, "desc"]],
                "columnDefs": [
                        { "orderable": false, "targets": 3 }
                      ],
                "processing": true,
                "serverSide": true,
                "ajax": "View/Logs/Datatable.php"
            });
        });

<?php else: ?>

     $(document).ready(function () {
                $('#table1').DataTable({
                    "columnDefs": [
                        { "orderable": false, "targets": 7 }
                      ],
                    "order": [[0, "desc"]],
                });
    });

<?php endif;?>

    $(document).on("click", ".close", function () {
        $(".close").parent().hide();
    });
</script>
<style>
    pre {
        white-space: pre-wrap !important;
    }
    .datatable {
        padding:0!important;
        margin-top:30px!important;
    }
    .desc_sec{
        padding: 15px;
        padding-top: 5px;
        padding-bottom: 25px;
    }
    h1.title_sec{
        padding: 15px;
        padding-bottom: 30px;
    }
    #main-menu ul.nav li {
        cursor: pointer;
    }
</style>
<?php
require_once '/usr/local/cpanel/php/WHM.php';
//@WHM::header('Tinocache', 0, 0);
//@WHM::header('Tinocache', 0, 1);
//@WHM::header('Tinocache', 1, 0);
@WHM::header('Tinocache', 1, 1);
?>
<?php
$pathParts = explode('/', $viewPath);
$ControllerNameIndex = sizeof($pathParts) - 2;
$currentControllerName = $pathParts[$ControllerNameIndex];

?>

<div id="topline-ventures-content">

    <h1 class="title_sec">Tinocache Plugin</h1>

    <div id="topline-ventures-alerts">
        <?php
            require_once dirname(__FILE__) . '/Blocks/header.php';
            require_once dirname(__FILE__) . '/Blocks/alert.php';
            require_once dirname(__FILE__) . '/Blocks/abort.php';
        ?>
    </div>

    <div id="main-menu" style="padding-bottom: 2%;">
        <ul class="nav nav-pills" >
            <?php foreach ($this->menuItems as $menuItem): ?>
                <li class="uib-tab nav-item <?php
                    if ($currentControllerName == $menuItem->class) {
                    	echo 'active';
                    }
                    ?>" id='menu-item'>
                    <a class="nav-link">
                        <form id="my-form" method="GET" style="display: none;">
                            <input type="hidden" name="controller" value="<?php echo $menuItem->class ?>">
                        </form>
                        <span id="tabCustomizeBranding">
                            <?php echo $menuItem->name ?>
                        </span>
                    </a>
                </li>
            <?php endforeach;?>
        </ul>
    </div>

    <div id="topline-ventures-body">
        <?php if (!isset($this->abort)) {
            	require_once $viewPath;
            }
          ?>

    </div>

    <div>
        <hr>
    </div>

</div>

<script>
    $(".title_sec").hide().html("");
</script>
<script>
    $('.nav-item').click(function () {
        $(this).find('form').submit();
    });
</script>
<?php
WHM::footer();

?>
