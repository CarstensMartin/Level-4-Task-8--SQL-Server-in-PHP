<?php

require 'secrets.php';
$con = sqlsrv_connect('localhost', [
    'Database'=>'example_db',
    'UID'=>DB_UID,
    'PWD'=>DB_PWD
]);

if ($con === false){
    echo 'Failed to connect to db: ' . sqlsrv_errors()[0]['message'];
    exit();
}

function check_err($var){
    if ($var === false){
        echo 'DB failure: ' . sqlsrv_errors()[0]['message'];
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['id'])){

        $id = $_POST['id'];

        $stmt = sqlsrv_prepare($con,
            'DELETE FROM todos WHERE id=?',
                [$id]
        );
        check_err($stmt);

        $res = sqlsrv_execute($stmt);
        check_err($res);

        echo '<p>Successfully deleted todo item</p>';

    }else{
        $new_title = $_POST['title'];

        $stmt = sqlsrv_prepare($con,
            'INSERT INTO todos(title) VALUES (?)',
            [$new_title]);
        check_err($stmt);

        $res = sqlsrv_execute($stmt);
        check_err($res);

        // success case
        echo '<p>Todo item successfully inserted</p>';
    }
}

?>

<h2>Todo list items</h2>
<table><tbody>
<tr><th>Item</th><th>Added on</th><th>Complete</th></tr>
<?php
$stmt = sqlsrv_query($con, 'SELECT id, title, created FROM todos');

while ($row = sqlsrv_fetch_array($stmt)){
    $title = $row['title'];
    $created = $row['created']->format('j F');
    $id = $row['id'];
    echo '<tr>';
    echo '<td>' . $title . '</td>';
    echo '<td>' . $created . '</td>';
    echo '<td><form method="post" action="todo.php">
            <input type="hidden" name="id" value="'.$id.'">
            <button type="submit">Done</button>
          </form></td>';
    echo '</tr>';
}

sqlsrv_close($con);
?>
</tbody></table>
<br/><br/>

<form method="post" action="todo.php">
    <input type="text" name="title" placeholder="Todo item">
    <button type="submit">Submit</button>
</form>

