<?php
/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */


// DB table to use
$table = 'tinocache_logs';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = [
    [
        'db'        => 'created_at',
        'dt'        => 0,
        'formatter' => function($d, $row) {
            return date('Y-m-d H:i:s', strtotime($d));
        }
    ],
    ['db' => 'action', 'dt' => 1],
    ['db' => 'request', 'dt' => 2],
    ['db' => 'response', 'dt' => 3],
    ['db' => 'id', 'dt' => 4]
];

// SQL server connection information
$sql_details = [
    'user' => '',
    'pass' => '',
    'db'   => '',
    'host' => ''
];


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.class.php');

$result = (array)SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns);

foreach ($result['data'] as &$r)
{
    $id   = (int)$r[4];
    $r[2] = $r[3];

    if (stripos($r[2], 'error') !== false || stripos($r[2], 'BAD_REQ') !== false || stripos($r[2], 'No such') !== false || stripos($r[2], 'Failure') !== false || stripos($r[2], "can't") !== false)
    {
        $r[2] = 'Failure';
    }
    else
    {
        $r[2] = 'Success';
    }

    $r[3] = "<a href='index.php?controller=Logs&amp;id=$id' class='btn btn-primary'>Details</a>";
}

echo json_encode($result);
