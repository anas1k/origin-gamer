<?php 

session_start();

include('../controller/products.php');
include('../controller/category.php');
include('../controller/users.php');


$AllProducts = GetProducts();

$FourProducts = FourProducts();

$AllCategories = GetCategory();

$ProductsCount = CountProducts() -> num_rows;

$UsersCount = GetUsers() -> num_rows;


$res = TotalQuantity() -> fetch_assoc();
$QuantityCount = $res['total'];


$StockNet = 0;
foreach($AllProducts as $product){
    $StockNet += (float)$product['price'] * (float)$product['quantity'];
}




// add product routing
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_REQUEST['addProductForm'])){
        extract($_POST);
        $idUser = $_SESSION['id'];
        if( empty($name) || empty($idCategory) || empty($price)  || empty($quantity)|| empty($description) ){
            $_SESSION['icon'] = "error";
            $_SESSION['message'] = "Veuillez remplir tous les champs";
            header('Location: ../core/allproducts.php'); //redirect to page
            die;
        }else{
            if ($_FILES['picture']['name'] != "") {
                $fileName = $_FILES['picture']['name'];
                $fileTmpName = $_FILES['picture']['tmp_name'];
                $fileSize = $_FILES['picture']['size'];
                $fileError = $_FILES['picture']['error'];
                $fileType = $_FILES['picture']['type'];
            
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $allowed = array('jpg', 'jpeg', 'png' , 'jfif');
            
                if (in_array($fileActualExt, $allowed)) {
                    if ($fileError === 0) {
                        if ($fileSize < 1728640) {  // 10MB max file size
                            $fileNameNew = date("dmy") . time() . "." . $fileActualExt; //create unique name using time and date and name of 'picture'
                            $fileDestination = "../assets/img/uploads/" . $fileNameNew;

                            $result = AddProduct($name, $idCategory, $idUser, $fileDestination, $price, $quantity, $description);
                            if($result == 1){
                                move_uploaded_file($fileTmpName, $fileDestination);
                                header('Location: '. $_SERVER['PHP_SELF']); //refresh page
                                die;
                            }
                        } else {
                            $_SESSION['message'] = "erreur";
                            $_SESSION['message'] = "La taille de fichier est trop grand!!";
                            header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                            die;
                        }
                    } else {
                        $_SESSION['message'] = "erreur";
                        $_SESSION['message'] = "Erreur de téléchargement de fichier!!";
                        header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                        die;
                    }
                } else {
                    $_SESSION['message'] = "erreur";
                    $_SESSION['message'] = "Erreur de type de fichier!!";
                    header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                    die;
                }
            }
        }
    }
}

// delete product routing
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_REQUEST['deleteProductForm'])){
        $id = $_REQUEST['deleteProductForm'];
        $result = DeleteProduct($id);
        if($result == 1){
            /* $_SESSION['icon'] = "error";
            $_SESSION['message'] = "Produit supprimé avec succès"; */
            die;
        }
    }
}

// edit product routing
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_REQUEST['updateProductForm'])){
        extract($_POST);
        $idUser = $_SESSION['id'];
        if( empty($name) || empty($price) || empty($quantity) || empty($idCategory) || empty($description) ){
            $_SESSION['icon'] = "error";
            $_SESSION['message'] = "Veuillez remplir tous les champs";
            header('Location: ../core/allproducts.php'); //redirect to page
            die;
        }else{
            if ($_FILES['picture']['name'] != "") {
                $fileName = $_FILES['picture']['name'];
                $fileTmpName = $_FILES['picture']['tmp_name'];
                $fileSize = $_FILES['picture']['size'];
                $fileError = $_FILES['picture']['error'];
                $fileType = $_FILES['picture']['type'];
            
                $fileExt = explode('.', $fileName);
                $fileActualExt = strtolower(end($fileExt));
                $allowed = array('jpg', 'jpeg', 'png' , 'jfif');
        
                if (in_array($fileActualExt, $allowed)) {
                    if ($fileError === 0) {
                        if ($fileSize < 10728640) {  // 10MB max file size
                            $fileNameNew = date("dmy") . time() ."." . $fileActualExt; //create unique name using time and date and name of 'picture'
                            $fileDestination = "../assets/img/uploads/" . $fileNameNew;

                            $result = EditProduct($id, $name, $idCategory, $idUser, $fileDestination, $price, $quantity, $description);
                            if($result == 1){
                                move_uploaded_file($fileTmpName, $fileDestination);
                                $_SESSION['message'] = "success";
                                $_SESSION['message'] = "Produit modifier par succes!!";
                                header('Location: ../core/allproducts.php'); //refresh page
                                die;
                            }
                        } else {
                            $_SESSION['message'] = "erreur";
                            $_SESSION['message'] = "La taille de fichier est trop grand!!";
                            header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                            die;
                        }
                    } else {
                        $_SESSION['message'] = "erreur";
                        $_SESSION['message'] = "Erreur de téléchargement de fichier!!";
                        header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                        die;
                    }
                } else {
                    $_SESSION['message'] = "erreur";
                    $_SESSION['message'] = "Erreur de type de fichier!!";
                    header('Location: ../core/allproducts.php'); //to avoid alerts when refresh page
                    die;
                }
            }else{
                $result = LastPicUpdate($id, $name, $idCategory, $idUser, $price, $quantity, $description);
                if($result == 1){
                    $_SESSION['message'] = "success";
                    $_SESSION['message'] = "Produit modifier par succes!!";
                    header('Location: ../core/allproducts.php'); //refresh page
                    die;
                }
            }
        }
    }
}

// add category routing
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_REQUEST['addCategoryForm'])){
        $name = $_REQUEST['name'];
        $result = AddCategory($name);
        if($result == 1){
            header('Location: '. $_SERVER['PHP_SELF']); //refresh page
            die;
        }

    }
}

// delete category routing
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_REQUEST['deleteCategoryForm'])){
        $id = $_REQUEST['deleteCategoryForm'];
        $result = DeleteCategory($id);
        if($result == 1){
            die;
        }
    }
}

// edit category routing
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_REQUEST['editCategoryForm'])){
        $id = $_REQUEST['id'];
        $name = $_REQUEST['name'];
        $result = EditCategory($id, $name);
        if($result == 1){
            header('Location: ../core/allcategories.php'); //refresh page
            die;
        }
    }
}

// check user routing
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_REQUEST['loginUser'])){
        $email = $_REQUEST['email'];
        $password = $_REQUEST['password'];
        $result = CheckUser($email, $password);
        if($result != 0){
            $_SESSION['id'] = $result['id'];
            $_SESSION['fullname'] = $result['fullname'];
            $_SESSION['email'] = $result['email'];
            
            header('Location: ../core/'); //refresh page
            die;
        }
    }
}

