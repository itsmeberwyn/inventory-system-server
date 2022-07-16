<?php
class Post
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory

    public function addCategory($category)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add category to database';

        $sql = 'INSERT INTO categories (categoryName, description) VALUES (?,?)';
        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $category->categoryName,
            $category->description,
        ]);

        $count = $sql->rowCount();

        if ($count) {
            $payload = $category;
            $code = 200;
            $remarks = 'success';
            $message = 'Product was successfully added to the database.';
        }

        return response($payload, $remarks, $message, $code);
    }

    public function addProduct($product)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add product to inventory';

        $sql = 'INSERT INTO products (categoryId, productName, productDescription, price, quantity, minQuantity, maxQuantity) VALUES (?,?,?,?,?,?,?)';

        $sql = $this->pdo->prepare($sql);
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
            $payload = $product;
            $code = 200;
            $remarks = 'success';
            $message = 'Product was successfully added to the database.';
        }

        return response($payload, $remarks, $message, $code);
    }

    // modal with view products list with search parameters
    public function addPurchases($product)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add new purchases';

        $datafields = ['purchaseSerialId', 'productId', 'supplierId', 'price', 'quantityBought'];

        try {
            $this->pdo->beginTransaction();

            $insert_values = array();
            foreach ($product as $d) {
                $question_marks[] = '('  . $this->placeholders('?', sizeof($datafields)) . ')';
                $insert_values[] = $d->purchaseSerialId;
                $insert_values[] = $d->productId;
                $insert_values[] = $d->supplierId;
                $insert_values[] = $d->price;
                $insert_values[] = $d->quantityBought;
            }

            $sql = "INSERT INTO purchases (" . implode(",", $datafields) . ") VALUES " .
                implode(',', $question_marks);

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($insert_values);

            $this->pdo->commit();

            $payload = $product;
            $code = 200;
            $remarks = 'success';
            $message = 'Purchases was successfully added to the database.';

            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }
        return response($payload, $remarks, $message, $code);
    }

    public function addSupplier($supplier)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to add supplier to the database.';

        $sql = 'INSERT INTO suppliers (supplierName, contact, location) VALUES (?,?,?)';

        $sql = $this->pdo->prepare($sql);
        $sql->execute([
            $supplier->supplierName,
            $supplier->contact,
            $supplier->location,
        ]);

        $count = $sql->rowCount();
        $supplierId = $this->pdo->lastInsertId();
        $date = date('Y-m-d h:i:s', time());

        $supplier->id = $supplierId;
        $supplier->created_at = $date;

        if ($count) {
            $payload = $supplier;
            $code = 200;
            $remarks = 'success';
            $message = 'Supplier was successfully added to the database.';
        }

        return response($payload, $remarks, $message, $code);
    }

    // pos
    public function addOrders($orders, $transactionId): bool
    {
        $datafields = ['productId', 'transactionId', 'quantity', 'subTotal'];

        $orderDetails = array();
        foreach ($orders as $d) {
            $question_marks[] = '('  . $this->placeholders('?', sizeof($datafields)) . ')';
            $orderDetails[] = $d->productId;
            $orderDetails[] = $transactionId;
            $orderDetails[] = $d->quantity;
            $orderDetails[] = $d->subTotal;
        }

        $sql = "INSERT INTO orders (" . implode(",", $datafields) . ") VALUES " .
            implode(',', $question_marks);

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($orderDetails);

        $count = $stmt->rowCount();

        if ($count) {
            return true;
        }

        return false;
    }

    public function addTransaction($order)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to create a new transaction';

        try {
            $this->pdo->beginTransaction();

            $sql = 'INSERT INTO transactions (amountReceive, totalAmount) VALUES (?,?)';
            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                $order->amountReceive,
                $order->totalAmount,
            ]);

            $transactionId = $this->pdo->lastInsertId();

            if ($this->addOrders($order->list, $transactionId)) {
                $payload = $order;
                $code = 200;
                $remarks = 'success';
                $message = 'Supplier was successfully added to the database.';

                $this->pdo->commit();
            } else {
                $this->pdo->rollback();
            }

            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return response($payload, $remarks, $message, $code);
    }

    protected function placeholders($text, $count = 0, $separator = ",")
    {
        $result = array();
        if ($count > 0) {
            for ($x = 0; $x < $count; $x++) {
                $result[] = $text;
            }
        }

        return implode($separator, $result);
    }
}
