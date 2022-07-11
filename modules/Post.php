<?php
class Post
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory
    public function addProduct($product)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add product to inventory';

        $sql = 'INSERT INTO inventory (categoryId, productName, productDescription, price, quantity, minQuantity, maxQuantity) VALUES (?,?,?,?,?,?,?)';

        $sql = $this->db->prepare($sql);
        $sql->execute([
            $product->categoryId,
            $product->productName,
            $product->productDescription,
            $product->price,
            $product->quantity,
            $product->minQuantity,
            $product->maxQuantity,
        ]);

        $count = $sql->rowCount();

        if ($count) {
            $payload = [$product];
            $code = 200;
            $remarks = 'success';
            $message = 'Product was successfully added to the database.';
        }

        return response($payload, $remarks, $message, $code);
    }

    public function addPurchase($product)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add purchase to the database.';

        $sql = 'INSERT INTO purchase (productId, supplierId, price, quantityBought) VALUES (?,?,?,?)';

        $sql = $this->db->prepare($sql);
        $sql->execute([
            $product->productId,
            $product->supplierId,
            $product->price,
            $product->quantityBought,
        ]);

        // new plan for this method
    }

    public function addSupplier($supplier)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add supplier to the database.';

        $sql = 'INSERT INTO supplier (supplierName, contact, location) VALUES (?,?,?)';

        $sql = $this->db->prepare($sql);
        $sql->execute([
            $supplier->supplierName,
            $supplier->contact,
            $supplier->location,
        ]);

        $count = $sql->rowCount();

        if ($count) {
            $payload = [$supplier];
            $code = 200;
            $remarks = 'success';
            $message = 'Supplier was successfully added to the database.';
        }

        return response($payload, $remarks, $message, $code);
    }

    // pos
    public function addOrder($order)
    {
        // new plan
    }

    public function addTransaction($order)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to create a new transaction';

        $datafields = ['amountReceive', 'totalAmount'];

        function placeholders($text, $count = 0, $separator = ",")
        {
            $result = array();
            if ($count > 0) {
                for ($x = 0; $x < $count; $x++) {
                    $result[] = $text;
                }
            }

            return implode($separator, $result);
        }

        try {
            $this->pdo->beginTransaction();

            $insert_values = array();
            foreach ($order as $d) {
                $question_marks[] = '('  . placeholders('?', sizeof($datafields)) . ')';
                $insert_values[] = $d->amountReceive;
                $insert_values[] = $d->totalAmount;
            }

            $sql = "INSERT INTO transactions (" . implode(",", $datafields) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($insert_values);

            $this->pdo->commit();

            $payload = $order;
            $code = 200;
            $remarks = 'success';
            $message = 'Supplier was successfully added to the database.';

            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return response($payload, $remarks, $message, $code);
    }
}
