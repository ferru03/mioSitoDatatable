<?php
    include "./pages/dataLayer.php";
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
    
    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD']; 

    $start = @$_POST["start"] ?? 0;
    $length = @$_POST["length"] ?? 10;

    $totalElements = get_totalElements();
    $totPages = get_totPages($totalElements, $length);

    $url = "http://localhost:8080/employees/index.php";
    
    $response = array(
        "data" => array(),
        "recordsTotal" => intval($totalElements)
    );
    
    switch($method){
        case 'GET':
        case 'POST': 
            $response["data"] = get($start, $length);
            $response["recordsFiltered"] = intval(filteredCount());
            echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            break;

        default:
            header("HTTP/1.1 400 BAD REQUEST");
            break;
    }

    function get_totalElements()
    {
        require ("./pages/database.php");
        
        $query = "SELECT count(*) FROM employees";

        $result = $mysqli-> query($query);
        $totE = $result-> fetch_row();

        return $totE[0];
    }

    function get_totPages($totalElements, $lenght)
    {
        require ("./pages/database.php");

        $totP = ceil($totalElements/$lenght) -1;
        return $totP;
    }

    function href($url, $page, $lenght){
        return $url . "?page=" . $page . "&size=" . $lenght;
    }

    function set_link($page, $lenght, $totPages, $url)
    {
        $links = array(
            "first" => array ( "href" => href($url, 0, $lenght)),
            "self" => array ( "href" => href($url, $page, $lenght), "templated" => true),
            "last" => array ( "href" => href($url, $totPages, $lenght))
        );
        
        if($page > 0){
            $links["prev"] = array( "href" => href($url, $page - 1, $lenght));
        }
        
        if($page < $totPages){
            $links["next"] = array ( "href" => href($url, $page + 1, $lenght));
        }
        
        return $links;
    }
?>
