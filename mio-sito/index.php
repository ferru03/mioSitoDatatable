<!DOCTYPE html>
<html>
    <head>
        <title>Ajax e datatable</title>
    </head>
    <body>
        <?php
        $method = $_SERVER['REQUEST_METHOD'];
        $servername = '172.17.0.1:3306';
        $username = 'root';
        $password = 'my-secret-pw';
        $dbname = "mydb";

        $conn = mysqli_connect($servername, $username, $password, $dbname);
        if (!$conn)
        {
            die("Could not connect MySql Server");
        }

        header('Content-Type: application/json');
        $page = @$_POST["start"] ?? 0;
        $size = @$_POST["length"] ?? 10;
        $id = @$_POST["id"];
        $totPages = 0;
        $query = "SELECT COUNT(employees.id) AS conteggio FROM employees";
        $result = mysqli_query($conn, $query);
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
            $totPages = $row["conteggio"];
        }

        $tot = ceil($totPages / $size);
        $url = "http://localhost:8090/index.php";

        function GET($page, $lenght){
            $query = "SELECT * FROM employees ORDER BY id LIMIT $page, $lenght";
            $rows = array();
    
            if($result = $mysqli-> query($query)){
                while($row = $result-> fetch_assoc()){
                    $rows[] = $row;
                }
            }
    
            return $rows;
        }

        function POST($first, $last, $gender){
            $query = "INSERT INTO employees (first_name, last_name, gender) VALUES ('$first', '$last', '$gender')";
            $result = $mysqli-> query($query);
    
        }
    
        function PUT($first, $last, $gender, $id){
            $query = "UPDATE employees SET first_name = '$first', last_name = '$last', gender = '$gender' WHERE id = $id";
            $result = $mysqli-> query($query);
            
        }
    
        function DELETE($id){
            $query = "DELETE FROM employees WHERE id = $id";
            $result = $mysqli-> query($query);
            
        }

        switch($method){

            case 'POST':

                $array['data'] = GET($page * $size, $size);
                echo json_encode($array);

                if($id != 0){
                    $array['_embedded']['employees'] = $id;
                    echo json_encode($array);
                }else{
                    $array['_embedded']['employees'] = GET($page * $size, $size);
                    echo json_encode($array);
                }

            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                PUT($data["first_name"], $data["last_name"], $data["gender"], $id);

                echo json_encode($data);
                break;

            case 'DELETE':
                DELETE($id);

                echo json_encode($array);
                break;
                
            default:
                header("ERROR!! BAD REQUEST");
                break;
        }

        function href($url, $page, $size, $tot){
            $last = $url . "?page=" . $page . $tot. "&size=" . $size;
            return $last;
        }

        function links($page, $size, $last, $url){
            $link = array(
                "first" => array ( "href" => href($url, 0, $size)),
                "self" => array ( "href" => href($url, $page, $size), "templated" => true),
                "last" => array ( "href" => href($url, $last, $size))
            );
            
            if($page > 0){
                $link["prev"] = array( "href" => href($url, $page - 1, $size));
            }
            
            if($page < $last){
                $link["next"] = array ( "href" => href($url, $page + 1, $size));
            }  
            return $link;
        }
        ?>
    </body>
</html>
