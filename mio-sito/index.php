<!DOCTYPE html>
<html>
    <head>
        <title>Ajax e datatable</title>
    </head>
    <body>
        <?php
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
        $id = @$_POST["id"] ?? 0;
        $searchVal = $_POST["search[value]"];
        $count = countRow();
        $results = countResults($searchVal);
        $baseurl = "http://localhost:8090/index.php";
        
        $method = $_SERVER['REQUEST_METHOD'];

        function countRow(){
            $query = "SELECT count(*) FROM employees";
    
            $result = $mysqli-> query($query);
            $row = $result-> fetch_row();
    
            return $row[0];
        }    
    
        function countResults($id){
            $query = "SELECT count(*) FROM employees WHERE id = $id";
            
            $result = $mysqli-> query($query);
            $row = $result-> fetch_row();
    
            return $row[0];
        }
    
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
    
        function GET_FILTERED($searchValue){
            $query = "SELECT * FROM employees WHERE id = $id";
    
            $rows = array();
    
            if($result = $mysqli-> query($query)){
                while($row = $result-> fetch_assoc()){
                    $rows[] = $row;
                }
            }
    
            return $rows;
        }
    
        function POST($firstN, $lastN, $g){
            $query = "INSERT INTO employees (first_name, last_name, gender) VALUES ('$firstN', '$lastN', '$g')";
            $result = $mysqli-> query($query);
    
        }
    
        function PUT($firstN, $lastN, $g, $id){
            $query = "UPDATE employees SET first_name = '$firstN', last_name = '$lastN', gender = '$g' WHERE id = $id";
            $result = $mysqli-> query($query);
            
        }
    
        function DELETE($id){
            $query = "DELETE FROM employees WHERE id = $id";
            $result = $mysqli-> query($query);
            
        }

        switch($method){

            case 'POST':

                if(!is_null($searchVal)){
                    $arrayJSON['data'] = GET_FILTERED($searchVal);
                    $arrayJSON['recordsFiltered'] = $count;
                    $arrayJSON['recordsTotal'] = $count;
                    echo json_encode($arrayJSON);
                }else{
                    $arrayJSON['data'] = GET($page*$size, $size);
                    $arrayJSON['recordsFiltered'] = $count;
                    $arrayJSON['recordsTotal'] = $count;
                    echo json_encode($arrayJSON);
                }
                break;
            case 'PUT':
                $data = json_decode(file_get_contents('php://input'), true);
                PUT($data["first_name"], $data["last_name"], $data["gender"], $id);

                echo json_encode($data);
                break;

            case 'DELETE':
                DELETE($id);

                echo json_encode($arrayJSON);
                break;
            
            default:
                header("ERROR!! BAD REQUEST");
                break;
        }

        function href($baseurl, $page, $size){
            return $baseurl . "?page=" . $page . "&size=" . $size;
        }

        function links($page, $size, $last, $baseurl){
            $links = array(
                "first" => array ( "href" => href($baseurl, 0, $size)),
                "self" => array ( "href" => href($baseurl, $page, $size), "templated" => true),
                "last" => array ( "href" => href($baseurl, $last, $size))
            );
            
            if($page > 0){
                $links["prev"] = array( "href" => href($baseurl, $page - 1, $size));
            }
            
            if($page < $last){
                $links["next"] = array ( "href" => href($baseurl, $page + 1, $size));
            }
            return $links;
        }
        ?>
    </body>
</html>
