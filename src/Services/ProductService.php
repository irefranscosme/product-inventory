<?php
namespace App\Services;

use App\Database\DB;
use App\Model\Product;
use App\Model\ProductInventory;
use Error;
use PDO;
use PDOException;

class ProductService {
    public function insert(ProductInventory | Product $product) {
        $db = new DB();
        $conn = $db->connection();
        
        try {
            $conn->beginTransaction();

            $productToInsert = "INSERT INTO products (name, unit, price, date_expiry, image) VALUES (:name, :unit, :price, :date_expiry, :image)";
            $productToInsertStmt = $conn->prepare($productToInsert);
            $productToInsertStmt->execute([
                ':name' => $product->getColumn("name"),
                ':unit' => $product->getColumn("unit"),
                ':price' => $product->getColumn("price"),
                ':date_expiry' => $product->getColumn("date_expiry") ? $product->getColumn("date_expiry") : NULL,
                ':image' => $this->imageUpload($product->getColumn("image"))
            ]);

            $productInventoryToInsert = "INSERT INTO product_inventory (product_id, quantity, cost) VALUES (:product_id, :quantity, :cost)";
            $productInventoryToInsertStmt = $conn->prepare($productInventoryToInsert);
            $productInventoryToInsertStmt->execute([
                ':product_id' => $conn->lastInsertId('product_id'),
                ':quantity' => $product->getColumn("quantity"),
                ':cost' => $product->getColumn("quantity") * $product->getColumn("price"),
            ]);
            echo json_encode(["message" => true]);
            $conn->commit();
       }catch(PDOException | Error $err) {
            echo json_encode(["message" => $err->getMessage()]);
       }
    }

    public function fetch($id) {
        $db = new DB();
        $conn = $db->connection();

        try {
            $conn->beginTransaction();
        
            if (!$id) {
                $productsToRetrieve = "SELECT 
                    p.product_id, 
                    p.name, 
                    p.unit, 
                    FORMAT(p.price, 2) as price,
                    DATE_FORMAT(p.date_expiry, \"%M %e, %Y\") as date_expiry,
                    pi.quantity,
                    p.image,
                    FORMAT(pi.cost, 2) as cost
                FROM 
                    products p
                INNER JOIN 
                    product_inventory pi 
                ON 
                    p.product_id = pi.product_id";
        
                $statement = $conn->prepare($productsToRetrieve);
                $statement->execute();
        
                $retrieveProducts = $statement->fetchAll(PDO::FETCH_ASSOC);
            } else {
                $productsToRetrieve = "SELECT 
                    p.product_id, 
                    p.name, 
                    p.unit, 
                    p.price, 
                    p.date_expiry, 
                    pi.quantity 
                FROM 
                    products p
                INNER JOIN 
                    product_inventory pi 
                ON 
                    p.product_id = pi.product_id 
                WHERE 
                    p.product_id = :product_id";
        
                $statement = $conn->prepare($productsToRetrieve);
                $statement->execute([':product_id' => $id]);
        
                $retrieveProducts = $statement->fetch(PDO::FETCH_ASSOC);
            }
            echo json_encode($retrieveProducts);
            $conn->commit();
       }catch(PDOException | Error $err) {
            echo json_encode(["message" => $err->getMessage()]);
       }
    }

    public function update(ProductInventory | Product $product, $id) {
        $db = new DB();
        $conn = $db->connection();
        

        try {
            $conn->beginTransaction();

            $imageName = $this->imageUpload($product->getColumn("image"));

            // Remove the old image
            $fetchLastUpdatedProduct = "SELECT * FROM products WHERE product_id = :product_id";
            $lastInsertedProductStatement = $conn->prepare($fetchLastUpdatedProduct);
            $lastInsertedProductStatement->execute([
                ':product_id' => $id,
            ]);

            $lastUpdatedProduct = $lastInsertedProductStatement->fetch(PDO::FETCH_ASSOC);
            
            if($product->getColumn("image")["error"]) {
                $updateProduct = "UPDATE products SET name=:name, unit=:unit, price=:price, date_expiry=:date_expiry WHERE product_id = :product_id";
                $ProductStatement = $conn->prepare($updateProduct);
                $ProductStatement->execute([
                    ':name' => $product->getColumn("name"),
                    ':unit' => $product->getColumn("unit"),
                    ':price' => $product->getColumn("price"),
                    ':date_expiry' => $product->getColumn("date_expiry") ? $product->getColumn("date_expiry") : NULL,
                    ':product_id' => $id,
                ]);
            } else {
                $updateProduct = "UPDATE products SET name=:name, unit=:unit, price=:price, date_expiry=:date_expiry, image=:image WHERE product_id = :product_id";
                $ProductStatement = $conn->prepare($updateProduct);
                $ProductStatement->execute([
                    ':name' => $product->getColumn("name"),
                    ':unit' => $product->getColumn("unit"),
                    ':price' => $product->getColumn("price"),
                    ':date_expiry' => $product->getColumn("date_expiry") ? $product->getColumn("date_expiry") : NULL,
                    ':image' => $imageName,
                    ':product_id' => $id,
                ]);
            }

            $insertProductInventory = "UPDATE product_inventory SET quantity=:quantity, cost=:cost WHERE product_id = :product_id";
            $ProductInventoryStatement = $conn->prepare($insertProductInventory);
            $ProductInventoryStatement->execute([
                ':quantity' => $product->getColumn("quantity"),
                ':cost' => $product->getColumn("quantity") * $product->getColumn("price"),
                ':product_id' => $id
            ]);

            if(!$product->getColumn("image")) {
                $this->removeImageFile($lastUpdatedProduct["image"]);
            }
            $this->removeNonExistingProductImage($imageName);
            echo json_encode(["message" => true]);
            $conn->commit();
       }catch(PDOException | Error $err) {
            echo json_encode(["message" => $err->getMessage()]);
       }
    }

    public function delete($id) {
        $db = new DB();
        $conn = $db->connection();
        
        try {
            $conn->beginTransaction();

            $productToDelete = "SELECT * FROM products WHERE product_id = :product_id";
            $productToDeleteStmt = $conn->prepare($productToDelete);
            $productToDeleteStmt->execute([
                ':product_id' => $id
            ]);

            $productToDeleteRecord = $productToDeleteStmt->fetch(PDO::FETCH_ASSOC);
            $this->removeImageFile($productToDeleteRecord["image"]);


            $deleteProduct = "DELETE FROM products  WHERE product_id = :product_id";
            $ProductStatement = $conn->prepare($deleteProduct);
            $ProductStatement->execute([
                ':product_id' => $id,
            ]);

            $this->removeNonExistingProductImage();
        
            echo json_encode(["message" => true]);
            $conn->commit();
       }catch(PDOException | Error $err) {
            echo json_encode(["message" => $err->getMessage()]);
       }
    }

    public function imageUpload($file) {
        if (!$file["error"]) {
            $path = __DIR__ . "/../../public/images/";
            $originalFileName = basename($file['name']);
        
            // Extract the extension
            $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        
            // Remove extension from the original filename
            $originalFileNameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);
        
            // Generate random letters
            $randomLetters = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 5); // You can adjust the number of random letters
        
            // Append random letters and extension after the original filename
            $newFileName = $originalFileNameWithoutExtension . $randomLetters . "." . $extension;
            $path = $path . $newFileName;
        
            // Validate file type
            $allowedImageTypes = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
            $detectedFileType = exif_imagetype($file['tmp_name']);

            try {
                if (in_array($detectedFileType, $allowedImageTypes)) {
                    if (move_uploaded_file($file['tmp_name'], $path)) {
                        return $newFileName;
                    } else {
                        throw new Error("There was an error uploading the file, please try again!");
                    }
                } else {
                    throw new Error("Invalid image file type. Only JPEG, PNG, and GIF are allowed.");
                }
            } catch(Error $err) {
                throw new Error($err->getMessage());
            }
        }
    }

    public function removeNonExistingProductImage($updatedProductImageName = "") {
        $db = new DB();
        $conn = $db->connection();
        
        $imagePath = __DIR__ . "/../../public/images"; 
        try {
            $conn->beginTransaction();

            // Check if the folder exists
            if (is_dir($imagePath)) {
                // Get all files in the folder
                $files = scandir($imagePath . "/");


                    foreach($files as $file) {
                        // check if the file is not a directory
                        if(
                           is_file($imagePath ."/".$file)
                        ) {
                            $fetchProducts = "SELECT * FROM products WHERE image=:image";
                            $ProductStatement = $conn->prepare($fetchProducts);
                            $ProductStatement->execute([
                                ":image" => $file,
                            ]);

                            // remove image file that is not linked with a product
                            if(count($ProductStatement->fetchAll(PDO::FETCH_ASSOC)) <= 0 && $updatedProductImageName !== $file) {
                                unlink($imagePath."/".$file);
                            }
                        }
                    }
                }

            $conn->commit();
       }catch(Error $err) {
            throw new Error($err->getMessage());
       }
    }

    public function removeImageFile($productImage) {
        $db = new DB();
        $conn = $db->connection();

        $imagePath = __DIR__ . "/../../public/images";
    
        // Check if the folder exists
        if (is_dir($imagePath)) {
            // Check if the file exists
            if(is_file($imagePath ."/". $productImage)) {
                unlink($imagePath ."/". $productImage);
            }
        }

    }
}