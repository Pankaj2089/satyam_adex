<?php

function quotationParseSize($size)
{
    $parts = preg_split('/\s*[xX×*]\s*/u', trim($size));
    if (count($parts) !== 2 || !is_numeric($parts[0]) || !is_numeric($parts[1]) || (float) $parts[0] <= 0 || (float) $parts[1] <= 0) {
        return null;
    }
    return round((float) $parts[0] * (float) $parts[1], 4);
}

function quotationBuildData(array $source)
{
    $descriptions = $source['description'] ?? [];
    $sizes = $source['size'] ?? [];
    $quantities = $source['quantity'] ?? [];
    $units = $source['unit'] ?? [];
    $prices = $source['price'] ?? [];
    $items = [];
    $totalQuantity = 0;
    $totalGst = 0;
    $totalAmount = 0;

    foreach ($descriptions as $index => $description) {
        $description = trim((string) $description);
        $size = trim((string) ($sizes[$index] ?? ''));
        $quantity = (float) ($quantities[$index] ?? 0);
        $unit = trim((string) ($units[$index] ?? ''));
        $price = (float) ($prices[$index] ?? -1);
        $isAreaUnit = in_array($unit, ['Sqf', 'Sqi'], true);
        $totalSqf = $isAreaUnit ? quotationParseSize($size) : null;

        if ($description === '' || $size === '' || !in_array($unit, ['Sqf', 'Sqi', 'Pcs'], true) || $quantity <= 0 || $price < 0 || ($isAreaUnit && $totalSqf === null)) {
            throw new InvalidArgumentException('Please complete every quotation item with a valid size, unit, quantity and price.');
        }

        $taxable = round(($isAreaUnit ? $totalSqf * $quantity : $quantity) * $price, 2);
        $gst = round($taxable * 0.18, 2);
        $amount = round($taxable + $gst, 2);
        $items[] = ['description' => $description, 'size' => $size, 'total_sqf' => $totalSqf, 'quantity' => $quantity, 'unit' => $unit, 'price' => $price, 'gst' => $gst, 'amount' => $amount];
        $totalQuantity += $quantity;
        $totalGst += $gst;
        $totalAmount += $amount;
    }

    if (!$items) {
        throw new InvalidArgumentException('Add at least one quotation item before generating the PDF.');
    }

    return [
        'quotation_no' => trim((string) ($source['quotation_no'] ?? '')),
        'quotation_date' => trim((string) ($source['quotation_date'] ?? date('Y-m-d'))),
        'place_of_supply' => trim((string) ($source['place_of_supply'] ?? '')),
        'customer_name' => trim((string) ($source['customer_name'] ?? '')),
        'customer_address' => trim((string) ($source['customer_address'] ?? '')),
        'customer_contact' => trim((string) ($source['customer_contact'] ?? '')),
        'customer_gstin' => trim((string) ($source['customer_gstin'] ?? '')),
        'customer_state' => trim((string) ($source['customer_state'] ?? '')),
        'feature_text' => trim((string) ($source['feature_text'] ?? '')),
        'items' => $items,
        'total_quantity' => $totalQuantity,
        'total_gst' => round($totalGst, 2),
        'total_amount' => round($totalAmount, 2),
    ];
}

function quotationSave(PDO $conn, array $data, $id = null)
{
    $conn->beginTransaction();
    try {
        $now = date('Y-m-d H:i:s');
        if ($id) {
            $stmt = $conn->prepare('UPDATE quotations SET quotation_no=?, quotation_date=?, place_of_supply=?, customer_name=?, customer_address=?, customer_contact=?, customer_gstin=?, customer_state=?, feature_text=?, total_quantity=?, total_gst=?, total_amount=?, updated_at=? WHERE id=?');
            $stmt->execute([$data['quotation_no'], $data['quotation_date'], $data['place_of_supply'], $data['customer_name'], $data['customer_address'], $data['customer_contact'], $data['customer_gstin'], $data['customer_state'], $data['feature_text'], $data['total_quantity'], $data['total_gst'], $data['total_amount'], $now, $id]);
            $conn->prepare('DELETE FROM quotation_items WHERE quotation_id=?')->execute([$id]);
        } else {
            $stmt = $conn->prepare('INSERT INTO quotations (quotation_no, quotation_date, place_of_supply, customer_name, customer_address, customer_contact, customer_gstin, customer_state, feature_text, total_quantity, total_gst, total_amount, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
            $stmt->execute([$data['quotation_no'], $data['quotation_date'], $data['place_of_supply'], $data['customer_name'], $data['customer_address'], $data['customer_contact'], $data['customer_gstin'], $data['customer_state'], $data['feature_text'], $data['total_quantity'], $data['total_gst'], $data['total_amount'], $now, $now]);
            $id = $conn->lastInsertId();
        }
        $stmt = $conn->prepare('INSERT INTO quotation_items (quotation_id, description, size, total_sqf, quantity, unit, price, gst, amount) VALUES (?,?,?,?,?,?,?,?,?)');
        foreach ($data['items'] as $item) {
            $stmt->execute([$id, $item['description'], $item['size'], $item['total_sqf'], $item['quantity'], $item['unit'], $item['price'], $item['gst'], $item['amount']]);
        }
        $conn->commit();
        return $id;
    } catch (Throwable $exception) {
        $conn->rollBack();
        throw $exception;
    }
}

function quotationFetch(PDO $conn, $id)
{
    $stmt = $conn->prepare('SELECT * FROM quotations WHERE id=?');
    $stmt->execute([$id]);
    $quotation = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$quotation) {
        return null;
    }
    $stmt = $conn->prepare('SELECT * FROM quotation_items WHERE quotation_id=? ORDER BY id');
    $stmt->execute([$id]);
    $quotation['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $quotation;
}