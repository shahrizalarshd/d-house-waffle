<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->orders;
    }

    /**
     * Column headers
     */
    public function headings(): array
    {
        return [
            'Order No',
            'Date',
            'Customer Name',
            'Unit No',
            'Items',
            'Quantity',
            'Subtotal (RM)',
            'Service Fee (RM)',
            'Total Amount (RM)',
            'Payment Method',
            'Payment Status',
            'Order Status',
            'Paid At',
        ];
    }

    /**
     * Map data for each row
     */
    public function map($order): array
    {
        return [
            $order->order_no,
            $order->created_at->format('d M Y H:i'),
            $order->buyer->name,
            $order->buyer->unit_no . ' - ' . $order->buyer->block,
            $order->items->pluck('product_name')->join(', '),
            $order->items->sum('quantity'),
            number_format($order->seller_amount, 2),
            number_format($order->platform_fee, 2),
            number_format($order->total_amount, 2),
            strtoupper($order->payment_method),
            strtoupper($order->payment_status),
            strtoupper($order->status),
            $order->paid_at ? $order->paid_at->format('d M Y H:i') : '-',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
