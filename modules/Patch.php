<?php

class Patch
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // inventory
    // update
    public function updateProduct($product)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to update product to inventory';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE products SET categoryId=?, productName=?, productDescription=?, price=?, minQuantity=?, maxQuantity=? WHERE id=?';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                $product->categoryId,
                $product->productName,
                $product->productDescription,
                $product->price,
                $product->minQuantity,
                $product->maxQuantity,
                $product->productId,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $product;
                $code = 200;
                $remarks = 'success';
                $message = 'Product was successfully updated to the database.';
            } else {
                $payload = $product;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was updated to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }

        return response($payload, $remarks, $message, $code);
    }

    public function updateSupplier($supplier)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to update product to inventory';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE suppliers SET supplierName=?, contact=?, location=? WHERE id=?';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                $supplier->supplierName,
                $supplier->contact,
                $supplier->location,
                $supplier->id,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $supplier;
                $code = 200;
                $remarks = 'success';
                $message = 'Product was successfully updated to the database.';
            } else {
                $payload = $supplier;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was updated to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }

        return response($payload, $remarks, $message, $code);
    }

    // delete
    public function deleteProduct($product)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to delete product to inventory';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE products SET is_deleted=? WHERE id=? AND is_deleted IS NULL';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                1,
                $product->productId,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $product;
                $code = 200;
                $remarks = 'success';
                $message = 'Product was deleted successfully to the database.';
            } else {
                $payload = $product;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was deleted to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }

        return response($payload, $remarks, $message, $code);
    }

    public function deleteSupplier($supplier)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to delete product to inventory';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE suppliers SET is_deleted=? WHERE id=? AND is_deleted IS NULL';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                1,
                $supplier->supplierId,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $supplier;
                $code = 200;
                $remarks = 'success';
                $message = 'Product was deleted successfully to the database.';
            } else {
                $payload = $supplier;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was deleted to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }
        return response($payload, $remarks, $message, $code);
    }

    public function updatePurchase($purchase)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to update transaction';

        try {
            $this->pdo->beginTransaction();

            foreach ($purchase as $d) {
                if ($d->id == 0) {
                    $sql = "INSERT INTO purchases (purchaseSerialId,productId,supplierId,price,quantityBought) VALUES (?,?,?,?,?)";
                    $sql = $this->pdo->prepare($sql);
                    $sql->execute([
                        $d->purchaseSerialId,
                        $d->productId,
                        $d->supplierId,
                        $d->price,
                        $d->quantityBought,
                    ]);
                } else {
                    $sql = "UPDATE purchases SET quantityBought=?, is_deleted=? WHERE id=?";
                    $sql = $this->pdo->prepare($sql);
                    $sql->execute([
                        $d->quantityBought,
                        $d->is_deleted,
                        $d->id,
                    ]);
                }
            }

            $count = $sql->rowCount();

            if ($count) {
                $payload = $purchase;
                $code = 200;
                $remarks = 'success';
                $message = 'Purchase was updated successfully to the database.';
            } else {
                $payload = $purchase;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was updated to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return response($payload, $remarks, $message, $code);
    }


    public function deletePurchase($purchase)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to delete purchase to database';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE purchases SET is_deleted=? WHERE purchaseSerialId=? AND is_deleted IS NULL';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                1,
                $purchase->purchaseId,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $purchase;
                $code = 200;
                $remarks = 'success';
                $message = 'Purcjase was deleted successfully to the database.';
            } else {
                $payload = $purchase;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was deleted to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }
        return response($payload, $remarks, $message, $code);
    }

    // pos
    // update
    public function updateOrder($orders)
    {
        foreach ($orders as $d) {
            if ($d->orderId != 0) {
                if ($d->quantity > $d->origQuantity && $d->is_deleted !== 1) {
                    $updateSql = "UPDATE products SET quantity=quantity-? WHERE id=?";
                    $updateSql = $this->pdo->prepare($updateSql);
                    $updateSql->execute([
                        abs($d->quantity - $d->origQuantity),
                        $d->productId,
                    ]);
                } else if ($d->quantity < $d->origQuantity && $d->is_deleted !== 1) {
                    $updateSql = "UPDATE products SET quantity=quantity+? WHERE id=?";
                    $updateSql = $this->pdo->prepare($updateSql);
                    $updateSql->execute([
                        abs($d->quantity - $d->origQuantity),
                        $d->productId,
                    ]);
                }

                if ($d->is_deleted === 1) {
                    $updateSql = "UPDATE products SET quantity=quantity+? WHERE id=?";
                    $updateSql = $this->pdo->prepare($updateSql);
                    $updateSql->execute([
                        abs($d->origQuantity),
                        $d->productId,
                    ]);
                }

                $sql = "UPDATE orders SET quantity=?, subTotal=?, is_deleted=? WHERE id=?";


                $sql = $this->pdo->prepare($sql);
                $sql->execute([
                    $d->quantity,
                    $d->subTotal,
                    $d->is_deleted,
                    $d->orderId,
                ]);
            } else {
                $updateSql = "UPDATE products SET quantity=quantity-$d->quantity WHERE id=$d->productId";
                $updateSql = $this->pdo->prepare($updateSql);
                $updateSql->execute([]);

                $sql = "INSERT INTO orders (productId, transactionId, quantity, subTotal) VALUES (?,?,?,?)";
                $sql = $this->pdo->prepare($sql);
                $sql->execute([
                    $d->productId,
                    $d->transactionId,
                    $d->quantity,
                    $d->subTotal,
                ]);
            }
        }

        $count = $sql->rowCount();

        if ($count) {
            return true;
        }
        return false;
    }

    public function updateTransaction($order)
    {
        $payload = [];
        $code = 424;
        $remarks = 'failed';
        $message = 'Failed to update transaction';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE transactions SET amountReceive=?, totalAmount=? WHERE id=?';
            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                $order->amountReceive,
                $order->totalAmount,
                $order->transactionId,
            ]);

            $count = $sql->rowCount();

            if ($this->updateOrder($order->list) || $count) {
                $payload = $order;
                $code = 200;
                $remarks = 'success';
                $message = 'Order was updated successfully to the database.';
            } else {
                $payload = $order;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was updated to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
            throw $e;
        }

        return response($payload, $remarks, $message, $code);
    }

    // delete
    public function deleteTransaction($order)
    {
        $payload = [];
        $code = 400;
        $remarks = 'failed';
        $message = 'Failed to delete order to database';

        try {
            $this->pdo->beginTransaction();

            $sql = 'UPDATE transactions SET is_deleted=? WHERE id=? AND is_deleted IS NULL';

            $sql = $this->pdo->prepare($sql);
            $sql->execute([
                1,
                $order->orderId,
            ]);

            $count = $sql->rowCount();

            if ($count) {
                $payload = $order;
                $code = 200;
                $remarks = 'success';
                $message = 'Order was deleted successfully to the database.';
            } else {
                $payload = $order;
                $code = 200;
                $remarks = 'success';
                $message = 'Nothing was deleted to the database.';
            }

            $this->pdo->commit();
            return response($payload, $remarks, $message, $code);
        } catch (\PDOException $e) {
            $this->pdo->rollback();
        }
        return response($payload, $remarks, $message, $code);
    }
}
